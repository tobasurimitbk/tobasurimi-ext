<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<style>
    .form-switch-lg .form-check-input {
        width: 4rem;
        height: 1.5rem;
    }
</style>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Duplikasi Proforma Invoice</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("proforma-invoice/detail/" . encrypt($dataSalesOrderExport->sales_order_export_id)); ?>">
                Kembali
            </a>
            <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                Duplikasi
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data" id="form-parent">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Detail Invoice</label>
                    </div>
                </div>
                <input autocomplete="one-time-code" value="<?= !empty($dataPI) ? encrypt($dataPI['id']) : '' ?>" type="hidden" class="id" name="id" id="id" />
                <input type="hidden" name="sales_order_export_id" id="sales_order_export_id" value="<?= $dataSalesOrderExport->sales_order_export_id ?>">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->no_invoice ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">No Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= date('d/m/Y', strtotime($dataSalesOrderExport->tanggal_invoice))  ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">Tanggal Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->sales_order_export_no ?>" autocomplete="one-time-code" disabled type="text" class="form-control">
                            <label for="floatingInput">No Order Form</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->customer_name ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Buyer / Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= $dataSalesOrderExport->address ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Alamat Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= strip_tags($dataSalesOrderExport->payment_term) ?>" autocomplete="one-time-code" disabled type="text" class="form-control no_invoice">
                            <label for="floatingInput">Payment Term</label>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Proforma Invoice</label>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" value="AUTO GENERATE" class="form-control no_invoice_pi" id="no_invoice_pi" name="no_invoice_pi" placeholder="No Invoice PI" required>
                                    <label for="floatingInput">No PI</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker tanggal_pi" id="tanggal_pi" name="tanggal_pi" placeholder="Tanggal PI" value="<?= !empty($dataPI) ? date('d/m/Y', strtotime($dataPI['tanggal_pi'])) : date('d/m/Y')  ?>">
                                    <label for="floatingInput">Tanggal PI</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select valas_id" id="valas_id" name="valas_id">
                                <option value=""></option>
                                <?php foreach ($dataValuta as $d) : ?>
                                    <option <?= !empty($dataPI) ? ($dataPI['valas_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d["id"]; ?>"><?= $d["value"]  ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Pilih Valas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_term_parent" id="payment_term_parent" name="payment_term_parent" placeholder="Payment Term"><?= !empty($dataPI) ? $dataPI['payment_term'] : strip_tags($dataSalesOrderExport->payment_term); ?></textarea>
                            <label for="floatingInput">Term Of Payment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="full-textarea form-control payment_instruction" id="payment_instruction" name="payment_instruction" placeholder="Payment Instruction"><?= !empty($dataPI) ? $dataPI['payment_instruction'] : '' ?></textarea>
                            <label for="floatingInput">Payment Instruction</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bank_id" id="bank_id" name="bank_id">
                                <option value=""></option>
                                <?php foreach ($dataBank as $d) : ?>
                                    <option <?= !empty($dataPI) ? ($dataPI['bank_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d["id"]; ?>"><?= $d["name"] . " - " . $d['atas_nama'] . " - " . $d['no_rekening']; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput">Pilih Bank</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataPI) ? $dataPI['packing'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control packing" id="packing" name="packing">
                            <label for="floatingInput">Packing</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input value="<?= !empty($dataPI) ? $dataPI['packing'] : '' ?>" autocomplete="one-time-code" type="text" class="form-control penanda_tangan" id="penanda_tangan" name="penanda_tangan">
                            <label for="floatingInput">Penanda Tangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input readonly autocomplete="one-time-code" type="text" class="form-control total_pi" id="total_pi" name="total_pi">
                            <label for="floatingInput">Total Proforma Invoice</label>
                        </div>
                    </div>
                </div>

                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold lable-title">List Barang Ekspor</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddBarang" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
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
                                    <th>Barang</th>
                                    <th style="width: 120px; text-align:right;">Qty</th>
                                    <th style="width: 120px; text-align:right;">Satuan</th>
                                    <th style="width: 120px; text-align:right;">Harga / Satuan</th>
                                    <th style="width: 120px; text-align:right;">Total Harga</th>
                                    <th style="width: 100px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-barang" id="body-barang">

                            </tbody>
                            <tfoot class="foot-barang" id="foot-barang">
                                <tr>
                                    <td colspan="7">List Barang Kosong</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-subtitle-modal mt-5">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold lable-title">List Payment Term</label>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-show-detail btn-add btn-block float-right" id="btnAddPaymentTerm" type="button" style="width: 90% !important;">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="paymentTermTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px;">No</th>
                                    <th>Keterangan Payment Term</th>
                                    <th style="width: 120px; text-align:right;">Nilai</th>
                                    <th style="width: 100px; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-payment-term" id="body-payment-term">

                            </tbody>
                            <tfoot class="foot-payment-term" id="foot-payment-term">
                                <tr>
                                    <td colspan="4">List Payment Term Kosong</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </form>

        </div>
    </div>
</section>

<div class="modal detail-modal" id="barangModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-modal-barang"></label> Barang</h5>
            </div>
            <form id="form-barang" role="form" method="POST">
                <input type="hidden" name="id_barang" id="id_barang">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang" placeholder="Nama Barang">
                                <label for="floatingInput">Nama Barang</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control qty_barang" id="qty_barang" name="qty_barang" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Qty Barang">
                                <label for="floatingInput">Qty</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select satuan_id" id="satuan_id" name="satuan_id">
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d) : ?>
                                        <option data-kode_satuan="<?= $d['kode_satuan'] ?>" value="<?= $d["id"]; ?>"><?= $d["kode_satuan"] . " - " . $d['nama_satuan']  ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label for="floatingInput">Pilih Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control harga_satuan" id="harga_satuan" name="harga_satuan" onkeyup="this.value = greatFormatRupiah(this.value)" placeholder="Harga Satuan Barang">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control total_harga" id="total_harga" name="total_harga" readonly placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideBarang">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitBarang">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="paymentTermModal" tabindex="1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label id="label-payment-term"></label> Payment Term</h5>
            </div>
            <form id="form-payment-term" role="form" method="POST">
                <input type="hidden" name="id_payment_term" id="id_payment_term">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 100px;">
                                <textarea name="payment_term" id="payment_term" class="form-control full-textarea payment_term" placeholder="Payment Term"></textarea>
                                <label for="floatingInput">Keterangan Payment Term</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="nilai_payment_term" name="nilai_payment_term" placeholder="Nilai Payment Term" onkeyup="this.value = greatFormatRupiah(this.value)">
                                <label for="floatingInput">Nilai Payment Term</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form">
                                <label class="mt-2 text-dark">
                                    <b>Masukkan ke Penagihan Proforma Invoice,</b> (Jika Aktif Maka Akan Dijadikan Penagihan Proforma Invoice Ini)
                                </label>
                                <div class="form-control border-0 custom-toggle-switch" style="margin-top: -15px;">
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" value="1" name="is_penagihan" id="is_penagihan">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHidePaymentTerm">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitPaymentTerm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listBarang = [];
    var listPaymentTerm = [];

    $(document).ready(function() {
        <?php if (!empty($dataPI)) { ?>
            <?php foreach ($dataPIBarang as $d): ?>
                listBarang.push({
                    id_barang: "<?= $d['id'] ?>",
                    nama_barang: "<?= str_replace(array("\r", "\n"), '', trim($d['nama_barang'])) ?>",
                    kode_satuan: "<?= $d['kode_satuan'] ?>",
                    satuan_id: "<?= $d['satuan_id'] ?>",
                    qty_barang: <?= floatval($d['qty_barang']) ?>,
                    harga_satuan: <?= floatval($d['harga_satuan']) ?>,
                    total_harga: <?= floatval($d['total_harga']) ?>
                });
            <?php endforeach ?>

            <?php foreach ($dataPIPaymentTerm as $d): ?>
                listPaymentTerm.push({
                    id_payment_term: "<?= $d['id'] ?>",
                    payment_term: "<?= $d['payment_term'] ?>",
                    nilai_payment_term: <?= floatval($d['nilai_payment_term']) ?>,
                    is_penagihan: <?= $d['is_penagihan'] ?>,
                });
            <?php endforeach ?>
            drawTableBarang(listBarang);
            drawTablePaymentTerm(listPaymentTerm);

        <?php } else { ?>

        <?php } ?>


        $("#tanggal_pi,#keberangkatan_kapal,#tanggal_surat_jalan,#tanggal_faktur_pajak").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('.valas_id').select2({
            placeholder: "Pilih Valas",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.bank_id').select2({
            placeholder: "Pilih Bank",
            theme: "bootstrap-5",
        }).change(function() {});

        $('.satuan_id').select2({
            placeholder: "Pilih Satuan",
            theme: "bootstrap-5",
            dropdownParent: $('#barangModal')
        }).change(function() {});

        //CSS SELECT2 FLOATING LABEL
        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.bank_id, .tax_id, .customer_id,.barang_id,.valas_id,.satuan_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $("#form-parent").validate({
            rules: {
                no_invoice_pi: {
                    required: true
                },
                tanggal_pi: {
                    required: true
                },
                valas_id: {
                    required: true
                },
                payment_term_parent: {
                    required: true
                },
                payment_instruction: {
                    required: true
                },
                bank_id: {
                    required: true
                },
                packing: {
                    required: true
                },
                penanda_tangan: {
                    required: true
                },
            },
            messages: {
                no_invoice_pi: {
                    required: "No Invoice PI wajib diisi"
                },
                tanggal_pi: {
                    required: "Tanggal PI wajib diisi"
                },
                valas_id: {
                    required: "Valas wajib diisi"
                },
                payment_term_parent: {
                    required: "Payment term wajib diisi"
                },
                payment_instruction: {
                    required: "Payment instruction wajib diisi"
                },
                bank_id: {
                    required: "Pilih bank"
                },
                packing: {
                    required: "Packing wajib diisi"
                },
                penanda_tangan: {
                    required: "Penanda tangan wajib diisi"
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

        var validatorBarang = $("#form-barang").validate({
            rules: {
                nama_barang: {
                    required: true
                },
                qty_barang: {
                    required: true
                },
                satuan_id: {
                    required: true
                },
                harga_satuan: {
                    required: true
                },
                total_harga: {
                    required: true
                },
            },
            messages: {
                nama_barang: {
                    required: "barang wajib diisi"
                },
                qty_barang: {
                    required: "qty barang wajib diisi"
                },
                satuan_id: {
                    required: "pilih satuan"
                },
                harga_satuan: {
                    required: "harga satuan wajib diisi"
                },
                total_harga: {
                    required: "total harga wajib diisi"
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

        var validatorPaymentTerm = $("#form-payment-term").validate({
            rules: {
                payment_term: {
                    required: true
                },
                nilai_payment_term: {
                    required: true
                },
            },
            messages: {
                payment_term: {
                    required: "Payment term wajib diisi"
                },
                nilai_payment_term: {
                    required: "Nilai payment term wajib diisi"
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


        $('#btnAddPaymentTerm').click(function(e) {
            e.preventDefault();
            $("#label-payment-term").text("Tambah ");
            $('#paymentTermModal').modal('show');
            resetFormPaymentTerm();
        });

        $('#btnAddBarang').click(function(e) {
            e.preventDefault();
            $('#label-modal-barang').text("Tambah ");
            $('#barangModal').modal('show');
            resetFormBarang();
        });

        $('#btnHideBarang').click(function(e) {
            e.preventDefault();
            $('#barangModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = qtyBarang * hargaSatuan;

            $('#total_harga').val(greatFormatRupiah(totalHarga.toFixed(2)));
        });

        $('#btnSubmitBarang').click(function(e) {
            e.preventDefault();
            if ($('#form-barang').valid()) {
                var idBarang = $('#id_barang').val();
                var namaBarang = $('#nama_barang').val();
                var satuanId = $('#satuan_id option:selected').val();
                var kodeSatuan = $('#satuan_id option:selected').data('kode_satuan');
                var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
                var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
                var totalHarga = destroyFormatRupiah($('#total_harga').val());

                if (idBarang) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listBarang.length; i++) {
                        if (listBarang[i].id_barang == idBarang) {
                            index = i;
                            break;
                        }
                    }

                    listBarang[index].nama_barang = namaBarang;
                    listBarang[index].qty_barang = qtyBarang;
                    listBarang[index].kode_satuan = kodeSatuan;
                    listBarang[index].satuan_id = satuanId;
                    listBarang[index].harga_satuan = parseFloat(hargaSatuan);
                    listBarang[index].total_harga = parseFloat(totalHarga);

                } else {
                    // CREATE
                    listBarang.push({
                        id_barang: getID(),
                        nama_barang: namaBarang,
                        kode_satuan: kodeSatuan,
                        satuan_id: satuanId,
                        qty_barang: parseFloat(qtyBarang),
                        harga_satuan: parseFloat(hargaSatuan),
                        total_harga: parseFloat(totalHarga)
                    });
                }

                $('#barangModal').modal('hide');
                drawTableBarang(listBarang);
            }
        });


        $('#btnHidePaymentTerm').click(function(e) {
            e.preventDefault();
            $('#paymentTermModal').modal('hide');
        });

        $('#qty_barang,#harga_satuan').keyup(function(e) {
            e.preventDefault();
            var qtyBarang = destroyFormatRupiah($('#qty_barang').val());
            var hargaSatuan = destroyFormatRupiah($('#harga_satuan').val());
            var totalHarga = parseFloat(qtyBarang) * parseFloat(hargaSatuan);
            $('#total_harga').val(greatFormatRupiah(totalHarga));
        });

        $('#btnSubmitPaymentTerm').click(function(e) {
            e.preventDefault();
            if ($('#form-payment-term').valid()) {
                var idPaymentTerm = $('#id_payment_term').val();
                var paymentTerm = $('#payment_term').val();
                var nilaiPaymentTerm = destroyFormatRupiah($('#nilai_payment_term').val());
                var isPenagihan = $('#is_penagihan').is(':checked');

                if (idPaymentTerm) {
                    // UPDATE
                    var index = null;
                    for (var i = 0; i < listPaymentTerm.length; i++) {
                        if (listPaymentTerm[i].id_payment_term == idPaymentTerm) {
                            index = i;
                            break;
                        }
                    }

                    listPaymentTerm[index].payment_term = paymentTerm;
                    listPaymentTerm[index].nilai_payment_term = parseFloat(nilaiPaymentTerm);
                    listPaymentTerm[index].is_penagihan = isPenagihan;

                } else {
                    // Create
                    idPaymentTerm = getID();
                    listPaymentTerm.push({
                        id_payment_term: idPaymentTerm,
                        payment_term: paymentTerm,
                        nilai_payment_term: parseFloat(nilaiPaymentTerm),
                        is_penagihan: isPenagihan,
                    });
                }

                drawTablePaymentTerm(listPaymentTerm);
                console.log(paymentTerm);
                $('#paymentTermModal').modal('hide');

            }
        });

        $(".btn-submit-parent").click(function() {
            var totalPi = destroyFormatRupiah($('#total_pi').val());
            if (listBarang.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan list barang ekspor !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (listPaymentTerm.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Wajib mengisikan payment term yang akan dibayar !',
                    confirmButtonColor: '#4e73df',
                })
            } else if (totalPi == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Total proforma invoice tidak boleh kosong !',
                    confirmButtonColor: '#4e73df',
                })
            } else {
                if ($("#form-parent").valid()) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Duplikasi Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Back',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrf = $(`[name="${csrfToken}"]`);
                            let url = "<?= base_url('proforma-invoice/create') ?>";
                            let data = new FormData(document.querySelector("#form-parent"));
                            let totalPI = destroyFormatRupiah($('#total_pi').val());

                            data.append("total_pi", totalPI);
                            data.append("listBarang", JSON.stringify(listBarang));
                            data.append("listPaymentTerm", JSON.stringify(listPaymentTerm));

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
                                                window.location.href = "<?= base_url("proforma-invoice/detail/" . encrypt($dataSalesOrderExport->sales_order_export_id)) ?>";
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
    })

    function resetFormPaymentTerm() {
        $('#id_payment_term').val(null);
        $('#payment_term').val(null);
        $('#nilai_payment_term').val(null);
        $('#is_penagihan').prop('checked', false);
    }

    function resetFormBarang() {
        $('#id_barang').val(null);
        $('#nama_barang').val(null);
        $('#satuan_id').val(null).change();
        $('#qty_barang').val(null);
        $('#harga_satuan').val(null);
        $('#total_harga').val(null);
    }

    function drawTableBarang(listBarang) {
        const table = $('#barangTable');
        const tbody = table.find('#body-barang');
        const tfoot = table.find('#foot-barang');

        tbody.empty();
        tfoot.empty();

        if (listBarang.length === 0) {
            tfoot.append(`
            <tr>
                <td colspan="7" >List Barang Kosong</td>
            </tr>
        `);
        } else {
            let totalHarga = 0;
            let totalQtyBarang = 0;
            let no = 1;

            listBarang.forEach(item => {
                totalHarga += item.total_harga;
                totalQtyBarang += item.qty_barang;
                const newRow = $(`
                <tr style="color:whitesmoke;">
                    <td class="text-center">${no++}</td>
                    <td>${item.nama_barang}</td>
                    <td class="text-right">${greatFormatRupiah(item.qty_barang)}</td>
                    <td class="text-right">${item.kode_satuan}</td>
                    <td class="text-right">${greatFormatRupiah(item.harga_satuan)}</td>
                    <td class="text-right">${greatFormatRupiah(item.total_harga)}</td>
                    <td class="text-center">
                    
                            <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowBarang('${item.id_barang}')">
                                <i class="fa fa-pencil fa-sm"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteRowBarang('${item.id_barang}')">
                                <i class="fa fa-trash fa-sm"></i>
                            </button>
                    </td>
                </tr>
            `);

                tbody.append(newRow);
            });

            // Tfoot rapi dan sesuai jumlah kolom
            tfoot.append(`
                <tr>
                    <th colspan="2" class="text-right">TOTAL</th>
                    <th class="text-right">${greatFormatRupiah(totalQtyBarang.toFixed(2))}</th>
                    <th colspan="2"></th>
                    <th class="text-right">${greatFormatRupiah(totalHarga.toFixed(2))}</th>
                    <th></th>
                </tr>
            `);

        }
    }

    function drawTablePaymentTerm(listPaymentTerm) {
        $('#body-payment-term').empty();
        $('#foot-payment-term').empty();
        var row = '';
        var no = 1;
        const table = $('#paymentTermTable');
        if (listPaymentTerm.length === 0) {
            row += `
                    <tr>
                        <td colspan="4">List Payment Term Kosong</td>
                    </tr>
                `;
            $('#foot-payment-term').append(row);
        } else {
            listPaymentTerm.map(item => {
                var bgcolor = 'whitesmoke';

                if (item.is_penagihan) {
                    bgcolor = '#ebe520ff';
                }
                var newRow = $('<tr style="background-color:' + bgcolor + ';color:whitesmoke;">');
                newRow.append($('<td style="text-align:center;">').text(no++));
                newRow.append($('<td>').text(item.payment_term));
                newRow.append($('<td class="text-right">').text(greatFormatRupiah(item.nilai_payment_term)));
                newRow.append($('<td class="text-right">').html(
                    `
                        <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detailRowPaymentTerm('${item.id_payment_term}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button type="button" class="btn btn-danger" onclick="deleteRowPaymentTerm('${item.id_payment_term}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                    `
                ));

                table.find('tbody').append(newRow);
            });
        }
        recalculatePIPayed(listPaymentTerm);
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
        $('#nama_barang').val(item.nama_barang);
        $('#satuan_id').val(item.satuan_id).change();
        $('#qty_barang').val(greatFormatRupiah(item.qty_barang));
        $('#harga_satuan').val(greatFormatRupiah(item.harga_satuan));
        $('#total_harga').val(greatFormatRupiah(item.total_harga));

        $('#label-modal-barang').text("Update ");
        $('#barangModal').modal('show');
    }

    function detailRowPaymentTerm(id_payment_term) {
        var item = null;
        for (var i = 0; i < listPaymentTerm.length; i++) {
            if (listPaymentTerm[i].id_payment_term == id_payment_term) {
                item = listPaymentTerm[i];
                break;
            }
        }

        $('#id_payment_term').val(item.id_payment_term);
        $('#payment_term').val(item.payment_term);
        $('#nilai_payment_term').val(greatFormatRupiah(item.nilai_payment_term));
        $('#is_penagihan').prop('checked', item.is_penagihan).change();

        $('#label-payment-term').text("Update ");
        $('#paymentTermModal').modal('show');
    }

    function deleteRowPaymentTerm(id_payment_term) {
        var indexToRemove = -1;
        for (var i = 0; i < listPaymentTerm.length; i++) {
            if (listPaymentTerm[i].id_payment_term == id_payment_term) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listPaymentTerm.splice(indexToRemove, 1);
        }
        drawTablePaymentTerm(listPaymentTerm);
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
            $('#no_invoice_pi').val("AUTO GENERATE");
            $('#no_invoice_pi').attr('readonly', true);
        } else {
            // SET NOMOR AUTO GENERATE FALSE
            $('#no_invoice_pi').val(null);
            $('#no_invoice_pi').attr('readonly', false);
        }

    }

    function recalculatePIPayed(listPaymentTerm) {
        var totalPaymentTerm = 0;
        $.each(listPaymentTerm, function(i, v) {
            if (v.is_penagihan) {
                totalPaymentTerm = totalPaymentTerm + v.nilai_payment_term;
            }
        });

        $('#total_pi').val(greatFormatRupiah(totalPaymentTerm));
    }

    const print = function(id) {
        window.open("<?= base_url('proforma-invoice/print') ?>" + '/' + id, "_blank");
    }

    function posting() {
        Swal.fire({
            icon: 'question',
            title: "Posting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("proforma-invoice/posting"); ?>",
                    data: {
                        id: $("#id").val(),
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                .then(() => {
                                    var salesOrderExportId = "<?= !empty($dataPI) ? encrypt($dataPI['sales_order_export_id']) : '' ?>";
                                    location.href = "<?= base_url('proforma-invoice/detail/') ?>" + salesOrderExportId;
                                })
                        }
                    },
                });
            }
        })

    }

    function unposting() {
        Swal.fire({
            icon: 'question',
            title: "Unposting Data ?",
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Back',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("proforma-invoice/unposting"); ?>",
                    data: {
                        id: $("#id").val(),
                    },
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        setLoading()
                    },
                    complete: function() {
                        stopLoading();
                    },
                    method: "POST",
                    dataType: "json",
                    success: function(response) {
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
        })

    }
</script>
<?= $this->endSection(); ?>