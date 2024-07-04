<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= (!empty($paymentData) ? 'Update Pembayaran PO Import' : 'Tambah Pembayaran PO Import') ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-import"); ?>">
                Batal
            </a>
            <?php if (empty($paymentData)) : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">
                    Simpan
                </button>
            <?php else : ?>
                <?php if ($paymentData['status_posting'] == "0") : ?>
                    <?php if (can('Pembayaran', 'Internasional', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($paymentData['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Internasional', 'p')) : ?>
                        <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-import/print/' . encrypt($paymentData['id']) ?? '') ?>">
                            Print
                        </a>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Internasional', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($paymentData['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Internasional', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Pembayaran', 'Internasional', 'p')) : ?>
                        <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-import/print/' . encrypt($paymentData['id']) ?? '') ?>">
                            Print
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <label class="form-label font-weight-bold lable-title mt-2 mb-3">
                Detail Pembayaran
            </label>
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($paymentData) ? encrypt($paymentData['id']) : "" ?>" />
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
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select " name="po_type" id="po_type">
                                <option disabled selected value=""></option>
                                <option value="BAHAN BAKU" <?= (!empty($paymentData) && $paymentData['po_type'] == 'BAHAN BAKU') ? 'selected' : '' ?>>BAHAN BAKU</option>
                                <option value="BAHAN PENOLONG" <?= (!empty($paymentData) && $paymentData['po_type'] == 'BAHAN PENOLONG') ? 'selected' : '' ?>>BAHAN PENOLONG</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Purchase Order</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select " name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($supplierList ?? [] as $supplier) : ?>
                                    <option value="<?= $supplier->id ?>" <?= (!empty($paymentData) && $paymentData['supplier_id'] == $supplier->id) ? 'selected' : '' ?>><?= $supplier->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($paymentData) ? ($paymentData['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <?php if (!empty($paymentData)) : ?>
                                <select class="form-select " name="import_po" id="import_po" <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?>>>
                                    <option selected value="<?= encrypt($poDetail->id) ?>"><?= $poDetail->po_no ?></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">PO Import</label>
                            <?php else : ?>
                                <select class="form-select " name="import_po" id="import_po" <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?>>
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
                    <!-- <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" oninput="preventNegativeInput(this)" type="text" class="form-control" id="payment_amt" <?= !empty($paymentData) ? "readonly" : "" ?> name="payment_amt" value="<?= "" . number_format($paymentData['payment_amt'] ?? 0, 2, ',', '.')  ?>">
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
                    </div> -->
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control" id="current_exchange_rate" name="current_exchange_rate" value="<?= "" . number_format($paymentData['current_exchange_rate'] ??  0, 2, ',', '.')  ?>">
                            <label for="floatingInput">Kurs saat ini</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" name="payment_date" type="text" value="<?= !empty($paymentData) ? date('d/m/Y', strtotime($paymentData['payment_date'])) : '' ?>" class="form-control payment_date" id="payment_date">
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
                                    <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" type="text" class="form-control" name="termin" id="termin" value="<?= !empty($paymentData) ? $paymentData['termin'] : '-' ?>" placeholder="Termin">
                                    <label for="floatingInput">Termin Pembayaran</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select " name="payment_method" id="payment_method">
                                <option selected value="">Pilih Payment Method</option>
                                <option value="CASH" <?= (!empty($paymentData) && $paymentData['payment_method'] == 'CASH') ? 'selected' : '' ?>>CASH</option>
                                <option value="BANK" <?= (!empty($paymentData) && $paymentData['payment_method'] == 'BANK') ? 'selected' : '' ?>>BANK</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" type="text" class="form-control" id="voucher_no" name="voucher_no" value="<?= !empty($paymentData) ? $paymentData['voucher_no'] : '-' ?>" placeholder="No. Voucher">
                                    <label for="floatingInput">No. Voucher</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select status_pph" name="status_pph" id="status_pph">
                                <option <?= !empty($paymentData) ? ($paymentData['status_pph'] == "1" ? 'selected' : '') : '' ?> value="1">PPH 2.5 %</option>
                                <option <?= !empty($paymentData) ? ($paymentData['status_pph'] == "0" ? 'selected' : '') : 'selected' ?> value="0">TIDAK ADA</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Status PPH</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" value="<?= !empty($paymentData) ? $paymentData['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" name="pembayaran_oleh" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating  mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select" name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($paymentData) ? ($paymentData['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating  mb-3" style="height: 50px;">
                            <select <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "disabled" : '')  : "" ?> class="form-select" name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($paymentData) ? ($paymentData['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
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
                                    <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '') : "" ?> autocomplete="one-time-code" type="text" name="no_invoice" class="form-control no_invoice" id="no_invoice" value="<?= $paymentData['no_invoice'] ?? '' ?>" placeholder="No Invoice">
                                    <label for="floatingInput">No Invoice (Opsional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" type="text" name="invoice_emkl" class="form-control" id="invoice_emkl" value="<?= $paymentData['invoice_emkl'] ?? '' ?>" placeholder="Invoice EMKL">
                                    <label for="floatingInput">No Invoice EMKL (Opsional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" type="text" name="no_aju" class="form-control" id="no_aju" value="<?= $paymentData['no_aju'] ?? '' ?>" placeholder="No Aju">
                                    <label for="floatingInput">No Aju (Opsional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea <?= !empty($paymentData) ? ($paymentData['status_posting'] == "1" ?  "readonly" : '')  : "" ?> autocomplete="one-time-code" name="note" class="form-control information text-area-all"><?= !empty($paymentData) ? $paymentData['note'] : '-' ?></textarea>
                            <label for="floatingInput">Note</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Pembayaran</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Potongan Panjar</button>
                        </li>

                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
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
                                                    <th style="text-align: center;">Input Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody class="body-detail-table" style="text-align: center;">
                                                <tr style="color: whitesmoke;">
                                                    <td colspan="5" style="text-align:center">Tidak ada Pembayaran</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-panjar" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th onclick="changeSort('no_panjar')">No. Panjar</th>
                                                <th onclick="changeSort('payment_date')">Payment Date</th>
                                                <th onclick="changeSort('payment_amount')">Total Panjar</th>
                                                <th>Sisa Panjar</th>
                                                <th>Bayar Panjar </th>
                                            </tr>

                                        </thead>
                                        <tbody class="body-table" id="body-table-panjar" style="cursor: pointer;">
                                            <tr style="color: whitesmoke;">
                                                <td colspan="7" style="text-align: center;">Tidak ada Panjar</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </div>

                    </div>

                </div>




            </form>



        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    listPanjar = [];
    listPembayaran = [];
    <?php if (!empty($paymentData)) : ?>

        var id = $('#id').val();
        $.ajax({
            url: '<?= base_url('/pembayaran-po-import/get-item-list/') ?>' + id,
            method: "GET",
            data: {
                status_pph: $('#status_pph').val(),
                id: $('#id').val()
            },
            dataType: "json",
            success: function(res) {
                listPembayaran = [];
                listPembayaran = res.data;

                listPanjar = [];
                listPanjar = res.panjar_data;

                drawPaidTable(res);
                drawPaidPanjarTable(res);
            }
        })
    <?php endif; ?>

    $("#payment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#import_po').select2({
        placeholder: "Pilih Nomor PO Import",
        theme: "bootstrap-5",
        allowClear: true,
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

        drawTable();

        // detailBarang.ajax.reload();
        // riwayatBayar.ajax.reload();
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

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#akun_kas').select2({
        placeholder: "Pilih Akun Kas",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#akun_selisih').select2({
        placeholder: "Pilih Akun Selisih (Opsional)",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#status_pph').select2({
        placeholder: "Status PPH",
        theme: "bootstrap-5"
    }).change(function() {

    });


    $('#supplier_id, #po_type, #divisi_id').change(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("po_type", $('#po_type').val());
        formData.append("supplier_id", $('#supplier_id').val());
        formData.append("divisi_id", $('#divisi_id').val());

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
            akun_kas: {
                required: true
            },
            status_pph: {
                required: true
            }
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
            akun_kas: {
                required: "Debit wajib diisi"
            },
            status_pph: {
                required: "Pilih status pph"
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

    $(".btn-submit-form").click(function() {


        $.each(listPanjar, function(i, v) {
            var element = $('input[data-id="' + v.id + '"].bayar_panjar');
            var input_user = convertRupiahToNumber(element.val());
            listPanjar[i].bayar_panjar = input_user;
        });
        $.each(listPembayaran, function(i, v) {
            var element = $('input[data-id="' + v.detail_id + '"].input_user');
            var input_user = convertRupiahToNumber(element.val());
            listPembayaran[i].pembayaran_user_input = input_user;
        });


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
                    let id = $('.id').val();
                    if (id) {
                        let formData = new FormData(document.querySelector(".create-form"));
                        formData.append("pembayaranList", JSON.stringify(listPembayaran));
                        formData.append("panjarList", JSON.stringify(listPanjar));
                        $.ajax({
                            url: "<?= base_url("pembayaran-po-import/update"); ?>",
                            data: formData,
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
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            location.reload();
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
                    } else {
                        let formData = new FormData(document.querySelector(".create-form"));
                        formData.append("pembayaranList", JSON.stringify(listPembayaran));
                        formData.append("panjarList", JSON.stringify(listPanjar));
                        $.ajax({
                            url: "<?= base_url("pembayaran-po-import/create"); ?>",
                            data: formData,
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

                }
            })
        }
    })

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var po_type = $('#po_type option:selected').val();
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

    function remove(id) {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        Swal.fire({
            icon: 'question',
            title: 'Hapus Pembayaran Ini ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-po-import/delete"); ?>",
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
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                        }).then((result) => {
                            window.location.href = "<?= base_url('pembayaran-po-import') ?>"
                        });

                    },
                });
            }
        });
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
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
                    url: "<?= base_url("pembayaran-po-import/posting"); ?>",
                    data: {
                        id: id,
                    },
                    beforeSend: function(xhr) {
                        setLoading();
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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
                                    location.reload()
                                })
                        }
                    },

                });
            }
        })
    }


    $('#supplier_id').change(function() {
        var supplierId = $('#supplier_id option:selected').val();
        $.ajax({
            url: `<?= base_url('/pembayaran-po-lokal/get-panjar'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                supplier_id: supplierId,
            },
            dataType: "json",
            success: function(res) {
                //after getting the data
                // console.log(res);
                listPanjar = [];
                listPanjar = res.data;
                appendPanjarNo(listPanjar);

                // console.log(listPanjar);
            }
        });
    });
    //append the panjar data
    function appendPanjarNo(data) {
        const tablePanjar = $('#dataTable-panjar');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (data.length > 0) {
            let no = 1;
            let found = false;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(data, function(i, v) {
                if (v.sisa_panjar_number > 0) {
                    found = true;

                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="width: 10px;">').text(no++));
                    newRow.append($('<td>').text(v.no_panjar));
                    newRow.append($('<td>').text((v.payment_date)));
                    newRow.append($('<td>').text((v.total_panjar)));
                    newRow.append($('<td>').text((v.sisa_panjar)));

                    newRow.append($('<td>').html(
                        `
                        <input  class="form-control bayar_panjar" onchange="this.value = formatRupiah(this.value)" oninput="limitInputBayar(this, ${v.sisa_panjar_number})" autocomplete="one-time-code" data-id="${v.id}"  type="text" value="" name = "bayar_panjar" style="height:40px">
                            `
                    ));
                    tablePanjar.find('tbody').append(newRow);
                }

            });
            if (!found) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
                tablePanjar.find('tbody').append(newRow);
            }
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }

    }



    function limitInputBayar(input, maxAmount) {
        var inputValue = input.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');

        // If numericValue is not a valid number, set it to '0'
        if (isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }

        // Convert numericValue to a float for comparison
        if (parseFloat(numericValue) > maxAmount) {
            input.value = maxAmount;
        }
    }

    function drawPaidPanjarTable(data) {
        const tablePanjar = $('#dataTable-panjar');
        const panjar_data = data.panjar_data


        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();

        if (panjar_data.length > 0) {
            let no = 1;
            $("#no_panjar").empty();
            tablePanjar.find('tbody').empty();

            $.each(panjar_data, function(i, v) {

                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="width: 10px;">').text(no++));
                newRow.append($('<td>').text(v.no_panjar));
                newRow.append($('<td>').text((v.payment_date)));
                newRow.append($('<td>').text(formatRupiah(v.total_panjar)));
                newRow.append($('<td>').text(formatRupiah(v.sisa_panjar)));

                newRow.append($('<td>').html(
                    `
                        <input <?= !empty($paymentData) ? ($paymentData['status_posting'] == 1 ? 'disabled' : '') : '' ?> onchange="this.value = formatRupiah(this.value)"  class="form-control bayar_panjar" oninput="limitInputBayar(this, ${Number(v.sisa_panjar)})" autocomplete="one-time-code" data-id="${v.id}"  class="form-control" type="text" value="${formatRupiah(v.bayar_panjar)}" name = "bayar_panjar" style="height:40px">
                            `
                ));
                tablePanjar.find('tbody').append(newRow);
            })
        } else {
            var newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td colspan="8" style="text-align:center;">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }

    }

    function drawPaidTable(res) {
        const table = $('#detailBarang');
        var statusPph = res.status_pph.status_pph;
        table.find('tbody').empty();
        var total_harga_semua = 0;
        var total_input_user = 0;

        var total_bayar_panjar = res.panjar_paid;



        $.each(res.data, function(i, v) {
            var newRow = $('<tr>');

            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.nama_barang));
            newRow.append($('<td>').text(v.qty_order));
            newRow.append($('<td>').text(v.total_harga));
            newRow.append($('<td>').html(
                `
                <div class="input-group d-flex align-items-center">
                    <input onchange="this.value = formatRupiah(this.value)" class="form-control input_user" oninput="limitInputBayar(this, ${v.input_user + v.sisa_pembayaran})" <?= !empty($paymentData) ? ($paymentData['status_posting'] == 1 ? 'disabled' : '') : '' ?> autocomplete="one-time-code" data-id="${v.detail_id}" type="text" value="${formatRupiah( v.input_user)}" name="input_user" style="height:40px">
                    <span class="input-group-text"  style="height:40px;">${res.currency}</span>
                </div>
                `
            ));



            table.find('tbody').append(newRow);
            total_harga_semua += Number(v.total_harga_number);
            total_input_user += Number(v.input_user);

        });

        var newRow1 = $('<tr style="color: white">');
        newRow1.append($('<td style="text-align:right;" colspan="3">').text('Total'));
        newRow1.append($('<td>').text(formatRupiah(total_harga_semua)));
        newRow1.append($('<td>').html(
            `
                <div class="input-group d-flex align-items-center">
                    <input autocomplete="one-time-code" data-id=""  class="form-control total-pembayaran trigger-input" type="text" value="  ${formatRupiah(total_input_user)}"  name = "total_pembayaran"  readonly>
                    <span class="input-group-text">${res.currency}</span>
                </div>
                `
        ));
        table.find('tbody').append(newRow1);

        var pph_result = 0;
        if (statusPph == 1) {
            pph_result = convertRupiahToNumber($(".total-pembayaran").val()) * 2.5 / 100;
        }

        var newRow2 = $('<tr>');
        newRow2.append($('<td style="text-align:right;" colspan="4"><b>Pajak Penghasilan (2.5 %) (+)</b></td>'));
        newRow2.append($('<td class="pph_amount">').text(formatRupiah(pph_result)));
        table.find('tbody').append(newRow2);

        var newRow3 = $('<tr>');
        newRow3.append($('<td style="text-align:right;" colspan="4"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));
        newRow3.append($('<td>').html(
            `
                <div class="input-group d-flex align-items-center">
                    <input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value=" ${formatRupiah(total_bayar_panjar)}" name = "total_pembayaran_panjar" readonly>
                    <span class="input-group-text">${res.currency}</span>
                </div>
                `
        ));

        table.find('tbody').append(newRow3);

        var grand_total = 0;
        grand_total = Number(total_input_user) + Number(pph_result) - Number(total_bayar_panjar)

        $('#status_pph').on('change', function() {
            if (status_pph == 1) {
                grand_total += Number(pph_result);
            } else if (status_pph == 0) {
                grand_total -= Number(pph_result);
            }
        });

        var newRow4 = $('<tr>');
        newRow4.append($('<td style="text-align:right;" colspan="4"><b>TOTAL PEMBAYARAN</b></td>'));
        newRow4.append($('<td>').html(
            `
                <div class="input-group d-flex align-items-center">
                    <input autocomplete="one-time-code" data-id=""   class="form-control grand-total" type="text" value="${formatRupiah(grand_total)}" name = "grand_total"  readonly>
                    <span class="input-group-text">${res.currency}</span>
                </div>
                `
        ));
        table.find('tbody').append(newRow4);

    }

    function drawTable() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        $.ajax({
            url: "<?= base_url("pembayaran-po-import/all-po"); ?>",
            method: "POST",
            dataSrc: "data",
            data: {
                po_id: $("#import_po").val(),
                po_type: $("#po_type").val(),
                status_pph: $("#status_pph").val()
            },
            beforeSend: function(xhr) {
                setLoading();
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                const table = $('#detailBarang');
                table.find('tbody').empty();
                var total_harga_semua = 0;
                listPembayaran = res;


                $.each(res, function(i, v) {

                    var newRow = $('<tr>');

                    newRow.append($('<td>').text(v.kode_barang));
                    newRow.append($('<td>').text(v.nama_barang));
                    newRow.append($('<td>').text(v.qty_order));
                    newRow.append($('<td>').text(v.total_harga));
                    newRow.append($('<td>').html(
                        `
        <input onchange="this.value = formatRupiah(this.value)"  class="form-control input_user" oninput="limitInputBayar(this, ${v.sisa_pembayaran})" autocomplete="one-time-code" data-id="${v.detail_id}"  class="form-control" type="text" value="" name = "input_user" style="height:40px">
            `
                    ));
                    table.find('tbody').append(newRow);
                    total_harga_semua += Number(v.total_harga_number);
                });
                var newRow1 = $('<tr>');
                newRow1.append($('<td style="text-align:right;" colspan="3">').text('Total'));
                newRow1.append($('<td>').text(formatRupiah(total_harga_semua)));
                newRow1.append($('<td style="text-align:right;" ><b>' +
                    '<input autocomplete="one-time-code"  class="form-control total-pembayaran trigger-input" type="text" value="" name = "total_pembayaran"  readonly>' +
                    '</b></td>'));
                table.find('tbody').append(newRow1);

                var newRow2 = $('<tr>');
                newRow2.append($('<td style="text-align:right;" colspan="4"><b>Pajak Penghasilan (2.5 %) (+)</b></td>'));

                newRow2.append($('<td class="pph_amount">').text("-"));

                table.find('tbody').append(newRow2);

                var newRow3 = $('<tr>');
                newRow3.append($('<td style="text-align:right;" colspan="4"><b>TOTAL PEMBAYARAN PANJAR</b></td>'));

                newRow3.append($('<td style="text-align:center;"><b>' +
                    '<input autocomplete="one-time-code"   class="form-control total-bayar-panjar trigger-input" type="text" value="" name = "total_pembayaran_panjar" readonly>' +
                    '</b></td>'));

                table.find('tbody').append(newRow3);

                var newRow4 = $('<tr>');
                newRow4.append($('<td style="text-align:right;" colspan="4"><b>TOTAL PEMBAYARAN</b></td>'));

                newRow4.append($('<td style="text-align:center;"><b>' +
                    '<input autocomplete="one-time-code"    class="form-control grand-total" type="text" value="" name = "grand_total"  readonly>' +
                    '</b></td>'));

                table.find('tbody').append(newRow4);
            }
        })
    }

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0,00";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
    }

    function convertRupiahToNumber(rupiah) {
        if (rupiah == "") {
            return 0;
        } else {
            var withoutDot = rupiah.replace(/\./g, '');
            var numberWithDot = withoutDot.replace(',', '.');
            return parseFloat(numberWithDot);
        }
    }


    $(document).on("input", ".bayar_panjar", function() {
        var sum = 0;
        $(".bayar_panjar").each(function() {
            sum += convertRupiahToNumber($(this).val());
        });
        $(".total-bayar-panjar").val(formatRupiah(sum));
        updateGrandTotal()

    });

    $(document).on("input", ".input_user", function() {
        var sum = 0;
        $(".input_user").each(function() {
            sum += convertRupiahToNumber($(this).val());
        });
        $(".total-pembayaran").val(formatRupiah(sum));
        updateGrandTotal()
    });


    $('#status_pph').on('change', function() {
        var status_pph = $(this).val();
        var total_pembayaran = convertRupiahToNumber($(".total-pembayaran").val());
        var result = total_pembayaran * 2.5 / 100;

        if (status_pph == 1) {
            $(".pph_amount").text(formatRupiah(result));
        } else if (status_pph == 0) {
            $(".pph_amount").text("0,00"); // Reset to original value
        }

        updateGrandTotal();
    });

    function updateGrandTotal() {
        var totalBayarPanjar = 0;
        if ($(".total-bayar-panjar").length) {
            totalBayarPanjar = convertRupiahToNumber($(".total-bayar-panjar").val()) || 0;
        }
        var totalPembayaran = convertRupiahToNumber($(".total-pembayaran").val());
        var pph_amount = convertRupiahToNumber($(".pph_amount").text());
        var total = totalPembayaran + pph_amount - Number(totalBayarPanjar);

        $(".grand-total").val(formatRupiah(total)); // Update the grand total input field with formatted value
    }
</script>

<?= $this->endSection(); ?>