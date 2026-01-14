<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran PO Lokal Bahan Penolong" : "Tambah Pembayaran PO Lokal Bahan Penolong" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal-bp"); ?>">
                Kembali
            </a>

            <?php if (!empty($detail)) : ?>
                <?php if (can('Transaksi Lokal', 'Lokal BP', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bp/print/' . encrypt($detail['id']) ?? '') ?>">
                        Print
                    </a>
                <?php endif; ?>

                <?php if ($detail['status_posting'] == "0") : ?>
                    <?php if (can('Transaksi Lokal', 'Lokal BP', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Transaksi Lokal', 'Lokal BP', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Transaksi Lokal', 'Lokal BP', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

            <?php else : ?>
                <?php if (can('Transaksi Lokal', 'Lokal BP', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['id']) : ""; ?>">

                <input type="hidden" name="tanda_terima_faktur_id" class="tanda_terima_faktur_id" value="">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required readonly <?= !empty($detail) ? ' value="' . $detail['payment_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> value="<?= !empty($detail) ? date('d/m/Y', strtotime($detail['payment_date'])) : '' ?>">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select"
                                <?= !empty($detail) && $detail['status_posting'] == 1 ? 'disabled' : '' ?>
                                name="jenis_pembayaran"
                                id="jenis_pembayaran"
                                required>
                                <?php if (empty($detail['jenis_pembayaran'])): ?>
                                    <option value="" selected disabled>Pilih Jenis Pembayaran</option>
                                <?php endif; ?>
                                <option value="MERAH" <?= !empty($detail['jenis_pembayaran']) && $detail['jenis_pembayaran'] == 'MERAH' ? 'selected' : '' ?>>MERAH</option>
                                <option value="PUTIH" <?= !empty($detail['jenis_pembayaran']) && $detail['jenis_pembayaran'] == 'PUTIH' ? 'selected' : '' ?>>PUTIH</option>
                            </select>
                            <label for="jenis_pembayaran">Jenis Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""; ?> name="bank_id" id="bank_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($bankList as $b) : ?>
                                    <option <?= !empty($detail) ? ($detail['bank_id'] == $b->id ? 'selected' : '') : '' ?> value="<?= $b->id ?>"><?= strtoupper($b->kode_bank) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kode Bank (Opsional)</label>
                        </div>
                    </div>
                   <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option 
                                        <?= !empty($detail) ? ($detail['supplier_id'] == $supplier['id'] ? 'selected' : '') : '' ?> 
                                        value="<?= $supplier['id'] ?>">
                                        <?= strtoupper($supplier['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> 
                                class="form-select divisi_id" 
                                id="divisi_id" 
                                name="divisi_id" 
                                aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($detail) ? ($detail['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id']; ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="tanda_terima_supplier[]" id="tanda_terima_supplier">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tanda Terima Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value=""></option>
                                <option <?= !empty($detail) ? ($detail['payment_method'] == "bank" ? 'selected' : '') : '' ?> value="Bank">BANK</option>
                                <option <?= !empty($detail) ? ($detail['payment_method'] == "cash" ? 'selected' : '') : '' ?> value="Cash">CASH</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <textarea class="form-control" name="supplier" id="supplier" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : '' ?> placeholder="supplier"><?= !empty($detail) ? $detail['supplier'] : ''  ?></textarea>
                            <label for="floatingInput" style="z-index: 1;">Pembayaran Ke</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <textarea class="form-control" name="keterangan" id="keterangan" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : '' ?> placeholder="Keterangan" style="height: 220%;"><?= !empty($detail) ? $detail['keterangan'] : ''  ?></textarea>
                            <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 50px;">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: center;">No</th>
                                                    <th style="text-align: center;">Tanggal LPB</th>
                                                    <th style="text-align: center;">No. Faktur</th>
                                                    <th style="text-align: center;">No. LPB</th>
                                                    <!-- <th style="text-align: center;">Barang</th>
                                                    <th style="text-align: center;">Qty</th>
                                                    <th style="text-align: center;">Satuan</th> -->
                                                    <th style="text-align: center;">Potongan</th>
                                                    <th style="text-align: center;">Tambahan</th>
                                                    <th style="text-align: center;">Total Tagihan</th>
                                                    <th style="text-align: center;">Total Di bayar</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body-table" style="text-align: center;">
                                                <tr style="color: whitesmoke;">
                                                    <td colspan="9">Tidak Ada Pembayaran</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>


    <div class="modal fade" id="adjustmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow">
                
                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="adjustmentTitle">
                        Penyesuaian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <input type="hidden" id="adjustmentId">
                    <input type="hidden" id="adjustmentType">

                    <!-- NOMINAL -->
                    <div class="form-floating mb-3">
                        <input type="text"
                            class="form-control text-start fw-bold"
                            id="adjustmentValue"
                            placeholder="0"
                            readonly
                            onkeyup="this.value = greatFormatRupiah(this.value)">
                        <label>Nominal</label>
                    </div>

                    <!-- DEBIT -->
                    <div class="form-floating mb-3">
                        <select class="form-control select2-ajax"
                            id="debit_account_id">
                        </select>
                        <label for="floatingInput" style="z-index: 1;">Debit</label>
                    </div>

                    <!-- KREDIT -->
                    <div class="form-floating mb-3">
                        <select class="form-control select2-ajax"
                            id="credit_account_id">
                        </select>
                       <label for="floatingInput" style="z-index: 1;">Kredit</label>
                    </div>



                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="saveAdjustment()">
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listPembayaran = [];

    

    <?php if (!empty($detail)) : ?>
        const tandaTerimaIds = <?= json_encode($detail['tanda_terima_faktur_ids'] ?? []) ?>;

        if (tandaTerimaIds.length > 0) {
            // 🔹 Trigger dulu load list tanda terima supplier
            getListTandaTerimaSupplier(function() {
                // 🔹 Setelah sukses load, baru set value dan trigger change
                $('#tanda_terima_supplier').val(tandaTerimaIds).trigger('change');

                // 🔹 Load data item list berdasarkan tandaTerimaIds
                $.ajax({
                    url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + tandaTerimaIds.join(','),
                    method: "GET",
                    data: {
                        status_pph: $('#status_pph').val(),
                        id: $('#id').val()
                    },
                    dataType: "json",
                    success: function (res) {
                listPembayaran = res.data;
                const table = $('#dataTable');
                const tbody = table.find('tbody');
                tbody.empty();

                if (!res.data || res.data.length === 0) {
                    tbody.html('<tr><td colspan="10" style="color: whitesmoke;">Tidak Ada Pembayaran</td></tr>');
                    return;
                }

                let no = 1;
                let grandTotalTagihan = 0;

                $.each(res.data, function (i, v) {
                    const tanggal = v.invoice_date.split('-');
                    const tglFormat = `${tanggal[2]}/${tanggal[1]}/${tanggal[0]}`;
                    const totalBayar = Number(v.total_paid || 0);
                    grandTotalTagihan += totalBayar;


                    const row = $('<tr>');
                    row.append($('<td>').text(no++));
                    row.append($('<td>').text(tglFormat));
                    row.append($('<td>').text(v.faktur_no));
                    row.append($('<td>').text(v.list_lpb));

                    // Di refreshTableAdjustment(), bagian render badge:
                    // Kolom potongan
                    row.append(`
                        <td class="text-center">
                            <span class="badge ${getAdjustmentBadgeClass(v.potongan, v.potongan_debit_account_id, v.potongan_credit_account_id)}
                                cursor-pointer px-3 py-2"
                                ${v.potongan > 0
                                    ? `onclick="openAdjustmentModal(${v.id}, 'potongan', ${v.potongan})"`
                                    : ''}
                            >
                                ${greatFormatRupiah(v.potongan)}
                            </span>
                        </td>
                    `);

                    // Kolom tambahan
                    row.append(`
                        <td class="text-center">
                            <span class="badge ${getAdjustmentBadgeClass(v.tambahan, v.tambahan_debit_account_id, v.tambahan_credit_account_id)}
                                cursor-pointer px-3 py-2"
                                ${v.tambahan > 0
                                    ? `onclick="openAdjustmentModal(${v.id}, 'tambahan', ${v.tambahan})"`
                                    : ''}
                            >
                                ${greatFormatRupiah(v.tambahan)}
                            </span>
                        </td>
                    `);

                    row.append($('<td>').text(greatFormatRupiah(v.nominal_faktur)));

                    // Kolom input pembayaran
                    const inputCell = $('<td>').html(`
                        <input type="text"
                            class="form-control form-control-sm text-end nominal_pembayaran"
                            style="font-weight:bold;"
                            onkeyup="this.value = greatFormatRupiah(this.value)"
                            oninput="limitInputBayar(this, ${v.nominal_faktur})"
                            value="${greatFormatRupiah(totalBayar)}"
                            data-id="${v.id}">
                    `);
                    row.append(inputCell);

                    tbody.append(row);
                });

                // Footer total
                const footerRow = $('<tr class="table-dark text-end">');
                footerRow.append($('<td></td>'));
                footerRow.append($('<td colspan="6"><b>GRAND TOTAL</b></td>'));
                footerRow.append($(`
                    <td>
                        <input type="text"
                            id="total_pembayaran"
                            name="total_pembayaran"
                            class="form-control form-control-sm text-end fw-bold"
                            value="${greatFormatRupiah(grandTotalTagihan)}"
                            readonly>
                    </td>
                `));
                tbody.append(footerRow);


                generateKeteranganPembayaran(res.data);
            }
                });
            });
        }
    <?php endif; ?>




    $(document).ready(function() {

        var validator = $(".create-form").validate({
            rules: {
                no_bukti_pembayaran: {
                    required: true
                },
                payment_date: {
                    required: true
                },
                nominal_faktur: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                keterangan: {
                    required: true
                },
                supplier: {
                    required: true
                },
                tanda_terima_supplier: {
                    required: true
                },
                nominal_pembayaran: {
                    required: true
                },
                jatuh_tempo: {
                    required: true
                },
                payment_method: {
                    required: true
                },
                // akun_kas: {
                //     required: true
                // },
                akun_selisih: {
                    required: true
                },
                jenis_pembayaran: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                status_pph: {
                    required: true
                },

            },
            messages: {
                no_bukti_pembayaran: {
                    required: "No. Pembayaran wajib diisi"
                },
                payment_date: {
                    required: "Tanggal pembayaran wajib diisi"
                },
                supplier_id: {
                    required: "Supplier wajib diisi"
                },
                tanda_terima_supplier: {
                    required: "Tanda terima supplier wajib diisi"
                },
                nominal_pembayaran: {
                    required: "Nominal pembayaran wajib diisi"
                },
                keterangan: {
                    required: "Keterangan pembayaran wajib diisi"
                },
                supplier: {
                    required: "Supplier pembayaran wajib diisi"
                },
                jatuh_tempo: {
                    required: "Tanggal jatuh tempo wajib diisi"
                },
                payment_method: {
                    required: "Metode pembayaran wajib diisi"
                },
                // akun_kas: {
                //     required: "Akun kas wajib diisi"
                // },
                akun_selisih: {
                    required: "Akun selisih wajib diisi"
                },
                jenis_pembayaran: {
                    required: "Jenis Pembayaran wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                }

            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errolacement: function(error, element) {
                var elem = $(element);
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
                $(element).closest('.col-md-4').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-4').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $('.jatuh_tempo_element').hide();

        // Di dalam $(document).ready(), perbaiki pemanggilan:
        $('#adjustmentModal').on('shown.bs.modal', function () {
            initSelect2Akun('#debit_account_id', 'Pilih Debit');
            initSelect2Akun('#credit_account_id', 'Pilih Kredit');
        });


        $("#payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        }).change(function() {
            generatePaymentNumber();
        });

        $('#akun_kas').select2({
            placeholder: "Akun Debit",
            theme: "bootstrap-5"
        });

        $('#payment_method').select2({
            placeholder: "Metode Pembayaran",
            theme: "bootstrap-5"
        }).change(function() {
            generatePaymentNumber();
        });

        $('#akun_selisih').select2({
            placeholder: "Akun Selisih (Opsional)",
            theme: "bootstrap-5"
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            generatePaymentNumber();
        });

        $('#tanda_terima_supplier').select2({
            placeholder: "Pilih Tanda Terima Supplier",
            theme: "bootstrap-5",
            multiple: true,
        }).change(function() {
            listBarangDetail();
        });

        $('#bank_id').select2({
            placeholder: "Pilih kode bank",
            theme: "bootstrap-5"
        }).change(function() {
            generatePaymentNumber();
        });

        $('#jenis_pembayaran').select2({
            placeholder: "Pilih Jenis Pembayaran",
            theme: "bootstrap-5"
        }).change(function() {
            generatePaymentNumber();
        });

        $('#supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5"
        }).change(function(e) {
            getListTandaTerimaSupplier();
            $('#supplier').val($('#supplier_id option:selected').text().trim());
        });

        $(document).on('keyup change', '.nominal_pembayaran', function () {
            let total = 0;
            $('.nominal_pembayaran').each(function () {
                const nominal = destroyFormatRupiah($(this).val()) || 0;
                total += nominal;
            });
            $('#total_pembayaran').val(greatFormatRupiah(total));
        });


        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            // Fungsi bantu buat konversi string ke angka
            const parseNominal = (val) => {
                const n = destroyFormatRupiah(val);
                return isNaN(n) ? 0 : Number(n);
            };

            // fungsi bantu biar listPembayaran selalu punya nominal_pembayaran
            const ensureNominalPembayaran = () => {
                listPembayaran = listPembayaran.map(item => ({
                    ...item,
                    nominal_pembayaran: Number(item.nominal_pembayaran ?? item.sisa_bayar ?? 0)
                }));
            };

            ensureNominalPembayaran();

            if (id) {
                // UPDATE MODE
                if ($(".create-form").valid()) {
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
                            let formData = new FormData(document.querySelector(".create-form"));
                            let totalPembayaran = 0;

                            $('.nominal_pembayaran').each(function() {
                                const id = $(this).data('id');
                                const nominal = parseNominal($(this).val());
                                const item = listPembayaran.find(i => i.id == id);
                                if (item) {
                                    // kalau user ubah nominal → simpan baru
                                    // kalau item lunas & input 0 → keep nilai lama
                                    if (nominal > 0) {
                                        item.nominal_pembayaran = nominal;
                                    } else if (!item.nominal_pembayaran) {
                                        item.nominal_pembayaran = 0;
                                    }
                                    totalPembayaran += Number(item.nominal_pembayaran) + Number(item.nominal_faktur)
                                }
                            });

                            $('#total_pembayaran').val(greatFormatRupiah(totalPembayaran));

                            formData.append("pembayaranList", JSON.stringify(listPembayaran));
                            formData.append("total_pembayaran", totalPembayaran);

                            $.ajax({
                                url: "<?= base_url("/pembayaran-po-lokal-bp/update"); ?>",
                                data: formData,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: stopLoading,
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
                                        }).then(() => {
                                            window.location.href = `<?= base_url("pembayaran-po-lokal-bp"); ?>`;
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            });
                        }
                    });
                }
            } else {
                // CREATE MODE
                if ($(".create-form").valid()) {
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
                            let formData = new FormData(document.querySelector(".create-form"));
                            let totalPembayaran = 0;

                            $('.nominal_pembayaran').each(function() {
                                const id = $(this).data('id');
                                const nominal = parseNominal($(this).val());
                                const item = listPembayaran.find(i => i.id == id);

                                if (item) {
                                    item.nominal_pembayaran = nominal;
                                    totalPembayaran += nominal;
                                }
                            });

                            $('#total_pembayaran').val(greatFormatRupiah(totalPembayaran));

                            formData.append("pembayaranList", JSON.stringify(listPembayaran));
                            formData.append("total_pembayaran", totalPembayaran);

                            $.ajax({
                                url: "<?= base_url("pembayaran-po-lokal-bp/create"); ?>",
                                data: formData,
                                beforeSend: function(xhr) {
                                    setLoading();
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                complete: stopLoading,
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
                                        }).then(() => {
                                            window.location.href = `<?= base_url("pembayaran-po-lokal-bp/id/"); ?>` + response.id;
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });

    })


    function getAdjustmentBadgeClass(value, debitId, creditId) {
        if (Number(value) <= 0) return 'bg-light text-muted';
        if (!debitId || !creditId) return 'bg-warning text-dark';
        return 'bg-success text-white';
    }



    function listBarangDetail() {
        const id = $('#tanda_terima_supplier').val();

        $.ajax({
            url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id,
            method: "GET",
            data: {
                status_pph: $('#status_pph').val()
            },
            beforeSend: setLoading,
            complete: stopLoading,
            dataType: "json",
            success: function (res) {
                listPembayaran = res.data.map(item => {
                    return {
                        ...item,
                        nominal_pembayaran: Number(item.nominal_pembayaran ?? item.sisa_bayar ?? 0)
                    };
                });
                const table = $('#dataTable');
                const tbody = table.find('tbody');
                tbody.empty();

                if (!res.data || res.data.length === 0) {
                    tbody.html('<tr><td colspan="10" style="color: whitesmoke;">Tidak Ada Pembayaran</td></tr>');
                    return;
                }

                let no = 1;
                let grandTotalTagihan = 0;

                $.each(res.data, function (i, v) {
                    const tanggal = v.invoice_date.split('-');
                    const tglFormat = `${tanggal[2]}/${tanggal[1]}/${tanggal[0]}`;
                    const totalBayar = Number(v.total_paid || 0);
                    grandTotalTagihan += totalBayar;


                    const row = $('<tr>');
                    row.append($('<td>').text(no++));
                    row.append($('<td>').text(tglFormat));
                    row.append($('<td>').text(v.faktur_no));
                    row.append($('<td>').text(v.list_lpb));

                    // MENJADI:
                    row.append(`
                        <td class="text-center">
                            <span class="badge ${getAdjustmentBadgeClass(v.potongan, v.potongan_debit_account_id, v.potongan_credit_account_id)}
                                cursor-pointer px-3 py-2"
                                ${Number(v.potongan) > 0
                                    ? `onclick="openAdjustmentModal(${v.id}, 'potongan', ${v.potongan})"`
                                    : ''}
                            >
                                ${greatFormatRupiah(v.potongan)}
                            </span>
                        </td>
                    `);

                    row.append(`
                        <td class="text-center">
                            <span class="badge ${getAdjustmentBadgeClass(v.tambahan, v.tambahan_debit_account_id, v.tambahan_credit_account_id)}
                                cursor-pointer px-3 py-2"
                                ${Number(v.tambahan) > 0
                                    ? `onclick="openAdjustmentModal(${v.id}, 'tambahan', ${v.tambahan})"`
                                    : ''}
                            >
                                ${greatFormatRupiah(v.tambahan)}
                            </span>
                        </td>
                    `);

                    row.append($('<td>').text(greatFormatRupiah(v.nominal_faktur)));

                    // Kolom input pembayaran
                    const inputCell = $('<td>').html(`
                        <input type="text"
                            class="form-control form-control-sm text-end nominal_pembayaran"
                            style="font-weight:bold;"
                            onkeyup="this.value = greatFormatRupiah(this.value)"
                            oninput="limitInputBayar(this, ${v.nominal_faktur})"
                            value="${greatFormatRupiah(totalBayar)}"
                            data-id="${v.id}">
                    `);
                    row.append(inputCell);

                    tbody.append(row);
                });

                // Footer total
                const footerRow = $('<tr class="table-dark text-end">');
                footerRow.append($('<td></td>'));
                footerRow.append($('<td colspan="6"><b>GRAND TOTAL</b></td>'));
                footerRow.append($(`
                    <td>
                        <input type="text"
                            id="total_pembayaran"
                            name="total_pembayaran"
                            class="form-control form-control-sm text-end fw-bold"
                            value="${greatFormatRupiah(grandTotalTagihan)}"
                            readonly>
                    </td>
                `));
                tbody.append(footerRow);


                generateKeteranganPembayaran(res.data);
            }
        });
    }

    function openAdjustmentModal(id, type, value) {
        console.log('openAdjustmentModal called:', {id, type, value});
        
        const rowData = listPembayaran.find(x => x.id == id);
        console.log('rowData:', rowData);
        
        $('#adjustmentId').val(id);
        $('#adjustmentType').val(type);
        $('#adjustmentValue').val(greatFormatRupiah(value));
        $('#adjustmentValue').prop('readonly', true);
        
        // Reset select2
        $('#debit_account_id').val(null).trigger('change');
        $('#credit_account_id').val(null).trigger('change');
        
        // 🔥 BEDAKAN BERDASARKAN TYPE
        if (type === 'potongan') {
            console.log('Loading POTONGAN accounts:', {
                debit: rowData.potongan_debit_account_id,
                credit: rowData.potongan_credit_account_id
            });
            
            if (rowData.potongan_debit_account_id) {
                setSelect2Value(
                    '#debit_account_id',
                    rowData.potongan_debit_account_id,
                    rowData.potongan_debit_account_name || ''
                );
            }
            
            if (rowData.potongan_credit_account_id) {
                setSelect2Value(
                    '#credit_account_id',
                    rowData.potongan_credit_account_id,
                    rowData.potongan_credit_account_name || ''
                );
            }
            
        } else if (type === 'tambahan') {
            console.log('Loading TAMBAHAN accounts:', {
                debit: rowData.tambahan_debit_account_id,
                credit: rowData.tambahan_credit_account_id
            });
            
            if (rowData.tambahan_debit_account_id) {
                setSelect2Value(
                    '#debit_account_id',
                    rowData.tambahan_debit_account_id,
                    rowData.tambahan_debit_account_name || ''
                );
            }
            
            if (rowData.tambahan_credit_account_id) {
                setSelect2Value(
                    '#credit_account_id',
                    rowData.tambahan_credit_account_id,
                    rowData.tambahan_credit_account_name || ''
                );
            }
        }
        
        $('#adjustmentModal').modal('show');
    }


    function saveAdjustment() {
        const id = $('#adjustmentId').val();
        const type = $('#adjustmentType').val();
        
        const debitAccountId = $('#debit_account_id').val();
        const creditAccountId = $('#credit_account_id').val();
        
        // Ambil TEXT dari Select2
        const debitSelect2 = $('#debit_account_id').select2('data');
        const creditSelect2 = $('#credit_account_id').select2('data');
        
        const debitAccountText = debitSelect2.length > 0 ? debitSelect2[0].text : '';
        const creditAccountText = creditSelect2.length > 0 ? creditSelect2[0].text : '';
        
        console.log('Saving adjustment:', {
            id, type,
            debitAccountId, debitAccountText,
            creditAccountId, creditAccountText
        });
        
        // 🔥 UPDATE KE PROPERTI YANG BERBEDA BERDASARKAN TYPE
        listPembayaran = listPembayaran.map(item => {
            if (item.id == id) {
                if (type === 'potongan') {
                    // Simpan ke akun potongan
                    item.potongan_debit_account_id = debitAccountId;
                    item.potongan_credit_account_id = creditAccountId;
                    item.potongan_debit_account_name = debitAccountText;
                    item.potongan_credit_account_name = creditAccountText;
                    
                    // Untuk kompatibilitas (jika perlu)
                    item.debit_account_id = debitAccountId;
                    item.credit_account_id = creditAccountId;
                    
                } else if (type === 'tambahan') {
                    // Simpan ke akun tambahan
                    item.tambahan_debit_account_id = debitAccountId;
                    item.tambahan_credit_account_id = creditAccountId;
                    item.tambahan_debit_account_name = debitAccountText;
                    item.tambahan_credit_account_name = creditAccountText;
                }
                
                console.log('Updated item:', item);
            }
            return item;
        });
        
        // Refresh tabel
        refreshTableAdjustment();
        
        $('#adjustmentModal').modal('hide');
        showToast('Akun debit/kredit berhasil disimpan', 'success');
    }


    function refreshTableAdjustment() {
        console.log('refreshTableAdjustment called, listPembayaran:', listPembayaran);
        
        const table = $('#dataTable');
        const tbody = table.find('tbody');
        tbody.empty();
        
        if (!listPembayaran || listPembayaran.length === 0) {
            tbody.html('<tr><td colspan="10" style="color: whitesmoke;">Tidak Ada Pembayaran</td></tr>');
            return;
        }
        
        let no = 1;
        let grandTotalTagihan = 0;
        
        $.each(listPembayaran, function (i, v) {
            // Format tanggal
            let tglFormat = '';
            if (v.invoice_date) {
                const tanggal = v.invoice_date.split('-');
                if (tanggal.length === 3) {
                    tglFormat = `${tanggal[2]}/${tanggal[1]}/${tanggal[0]}`;
                }
            }
            
            const totalBayar = Number(v.total_paid || 0);
            grandTotalTagihan += totalBayar;
            
            // Gunakan nilai yang benar
            const potonganValue = Number(v.potongan || 0);
            const tambahanValue = Number(v.tambahan || 0);
            
            const row = $('<tr>');
            row.append($('<td>').text(no++));
            row.append($('<td>').text(tglFormat));
            row.append($('<td>').text(v.faktur_no || ''));
            row.append($('<td>').text(v.list_lpb || ''));
            
            // Kolom potongan - gunakan nilai sebenarnya
            row.append(`
                <td class="text-center">
                    <span class="badge ${getAdjustmentBadgeClass(v.potongan, v.potongan_debit_account_id, v.potongan_credit_account_id)}
                        cursor-pointer px-3 py-2"
                        ${Number(v.potongan) > 0
                            ? `onclick="openAdjustmentModal(${v.id}, 'potongan', ${v.potongan})"`
                            : ''}
                    >
                        ${greatFormatRupiah(v.potongan)}
                    </span>
                </td>
            `);

            row.append(`
                <td class="text-center">
                    <span class="badge ${getAdjustmentBadgeClass(v.tambahan, v.tambahan_debit_account_id, v.tambahan_credit_account_id)}
                        cursor-pointer px-3 py-2"
                        ${Number(v.tambahan) > 0
                            ? `onclick="openAdjustmentModal(${v.id}, 'tambahan', ${v.tambahan})"`
                            : ''}
                    >
                        ${greatFormatRupiah(v.tambahan)}
                    </span>
                </td>
            `);
            
            row.append($('<td>').text(greatFormatRupiah(v.nominal_faktur || 0)));
            
            // Input pembayaran
            const inputCell = $('<td>').html(`
                <input type="text"
                    class="form-control form-control-sm text-end nominal_pembayaran"
                    style="font-weight:bold;"
                    onkeyup="this.value = greatFormatRupiah(this.value)"
                    oninput="limitInputBayar(this, ${v.nominal_faktur})"
                    value="${greatFormatRupiah(totalBayar)}"
                    data-id="${v.id}">
            `);
            row.append(inputCell);
            
            tbody.append(row);
        });
        
        // Footer total
        const footerRow = $('<tr class="table-dark text-end">');
        footerRow.append($('<td></td>'));
        footerRow.append($('<td colspan="6"><b>GRAND TOTAL</b></td>'));
        footerRow.append($(`
            <td>
                <input type="text"
                    id="total_pembayaran"
                    name="total_pembayaran"
                    class="form-control form-control-sm text-end fw-bold"
                    value="${greatFormatRupiah(grandTotalTagihan)}"
                    readonly>
            </td>
        `));
        tbody.append(footerRow);
        
        console.log('Table refreshed');
    }

    function setSelect2Value(selector, id, text) {
        if (!id) return;
        
        // Jika text sudah ada (dari listPembayaran)
        if (text) {
            // Hapus spasi ekstra di awal/akhir
            text = text.trim();
            
            // Cek apakah option sudah ada
            let optionExists = false;
            $(selector).find('option').each(function() {
                if ($(this).val() == id) {
                    optionExists = true;
                    return false; // break loop
                }
            });
            
            if (!optionExists) {
                // Tambah option baru
                const newOption = new Option(text, id, true, true);
                $(selector).append(newOption);
            }
            
            // Set value dan trigger change
            $(selector).val(id).trigger('change');
        } else {
            // Jika text tidak ada, load via AJAX untuk mendapatkan text
            $.ajax({
                url: "<?= base_url('/sub-account/dropdown-new'); ?>",
                method: "GET",
                data: { 
                    search: '',
                    id: id // Kirim ID untuk mencari spesifik
                },
                dataType: "json",
                success: function(response) {
                    if (response.results && response.results.length > 0) {
                        // Cari item dengan id yang sesuai
                        const item = response.results.find(r => r.id == id);
                        if (item) {
                            const newOption = new Option(item.text, id, true, true);
                            $(selector).append(newOption).trigger('change');
                        }
                    }
                }
            });
        }
    }

    function initSelect2Akun(selector, placeholderText) {
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: placeholderText,
            allowClear: true,
            dropdownParent: $('#adjustmentModal'),
            ajax: {
                url: "<?= base_url('/sub-account/dropdown-new'); ?>",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    // 🔥 LANGSUNG pakai data.results karena sudah format yang benar
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 3
        });
    }

    function validateBeforeSubmit() {
        // Cek potongan yang belum ada akun
        const potonganInvalid = listPembayaran.some(item =>
            Number(item.potongan || 0) > 0 &&
            (!item.potongan_debit_account_id || !item.potongan_credit_account_id)
        );
        
        // Cek tambahan yang belum ada akun
        const tambahanInvalid = listPembayaran.some(item =>
            Number(item.tambahan || 0) > 0 &&
            (!item.tambahan_debit_account_id || !item.tambahan_credit_account_id)
        );
        
        if (potonganInvalid) {
            alert('Masih ada potongan yang belum dipilih akun Debit/Kredit');
            return false;
        }
        
        if (tambahanInvalid) {
            alert('Masih ada tambahan yang belum dipilih akun Debit/Kredit');
            return false;
        }
        
        return true;
    }


    function showToast(message, type = 'success') {
        // Hapus toast sebelumnya
        $('.toast').remove();
        
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        setTimeout(() => toast.remove(), 3000);
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
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('id', id);
                $.ajax({
                    url: "<?= base_url("pembayaran-po-lokal-bp/delete"); ?>",
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
                            location.reload();
                        });

                    },
                });
            }
        });
    }

    function posting(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Pembayaran ?',
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
                    url: "<?= base_url("pembayaran-po-lokal-bp/posting"); ?>",
                    data: {
                        id: id,
                        divisi_id: $('#divisi_id option:selected').val(),
                        status: 1,
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

    function getListTandaTerimaSupplier(callback = null) {
        // get vat
        var supplier_id = $('#supplier_id').val();

        if (supplier_id != "") {
            var table = $('#dataTable');
            table.find('tbody').empty();
            $("#tanda_terima_supplier").empty();
            $.ajax({
                url: '<?= base_url('pembayaran-po-lokal-bp/get-rekap-faktur/') ?>' + supplier_id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.data.length == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Nomor tanda terima supplier tidak ada',
                            confirmButtonColor: '#4e73df',
                        });
                    } else {
                        $("#tanda_terima_supplier").append(`<option value=""></option>`);
                        res.data.forEach(function(item) {
                            $("#tanda_terima_supplier").append(`<option  value="${item.id}">${item.faktur_no}</option>`);
                        });
                    }

                    // Jalankan callback kalau ada
                    if (typeof callback === 'function') callback();
                }
            });
        }

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


    function limitInputBayar(input, maxAmount) {

        var inputValue = input.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }
        if (numericValue > maxAmount) {
            input.value = maxAmount;
        }
    }

    function updateSubTotal(newSubtotal) {
        // Ambil nilai subtotal yang saat ini ditampilkan di UI
        let currentSubtotalText = $('.subtotalNominal').text();
        console.log("Current Subtotal Text:", currentSubtotalText); // Debug

        // Hapus format Rupiah dari currentSubtotalText dan konversi ke angka
        let currentSubtotal = destroyFormatRupiah(currentSubtotalText);
        console.log("Current Subtotal (Number):", currentSubtotal); // Debug

        // Hapus format Rupiah dari newSubtotal dan konversi ke angka
        let newSubtotalNumber = destroyFormatRupiah(newSubtotal);
        console.log("New Subtotal (Number):", newSubtotalNumber); // Debug

        // Hitung subtotal baru (currentSubtotal - newSubtotalNumber)
        let updatedSubtotal = currentSubtotal - newSubtotalNumber;
        console.log("Updated Subtotal:", updatedSubtotal); // Debug

        // Perbarui nilai global
        subCountTotal = updatedSubtotal;

        // Tampilkan nilai subtotal yang baru ke UI dengan format Rupiah
        $('.subtotal').text(greatFormatRupiah(updatedSubtotal));
    }

    function formatDate(dateString) {
        let parts = dateString.split("-");
        let reversedParts = parts.reverse();
        let formattedDate = reversedParts.join("/");
        return formattedDate;
    }

    function generateKeteranganPembayaran(data) {
        let keterangan = "";

        // Ambil nama supplier
        const supplierName = $('#supplier_id option:selected').text().trim() || '-';

        // Ambil semua teks tanda terima (bisa multiple)
        const noTandaTerimaArr = $('#tanda_terima_supplier option:selected').map(function () {
            return $(this).text().trim();
        }).get();

        // Buat jadi satu string, pisahkan dengan koma
        const noTandaTerimaText = noTandaTerimaArr.join(", ") || '-';

        // Ambil semua LPB unik (kalau dalam data ada duplikat)
        let lpbList = [];
        $.each(data, function (i, v) {
            if (v.list_lpb) {
                // Pecah kalau ada lebih dari satu LPB di field (misal "LPB-01, LPB-02")
                const lpbItems = v.list_lpb.split(',').map(l => l.trim());
                lpbItems.forEach(lpb => {
                    if (lpb && !lpbList.includes(lpb)) {
                        lpbList.push(lpb);
                    }
                });
            }
        });

        // Gabungkan LPB jadi string
        const lpbText = lpbList.length > 0 ? lpbList.join(", ") : '-';

        // Susun keterangan pembayaran
        keterangan = `Pembayaran ${supplierName}; No TTS: ${noTandaTerimaText}; LPB: ${lpbText}`;

        // Set ke input keterangan
        $('#keterangan').val(keterangan);
    }

    function generatePaymentNumber() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("type", "Bahan Penolong");
        formData.append("payment_date", $("#payment_date").val());
        formData.append("id", $("#id").val());
        formData.append("noTransaksi", $("#no_bukti_pembayaran").val());
        formData.append("bankId", $("#bank_id option:selected").val());
        formData.append("divisiId", $("#divisi_id option:selected").text());
        formData.append("divisiIdInt", $("#divisi_id option:selected").val());
        formData.append("jenisPembayaran", $("#jenis_pembayaran option:selected").text());
        formData.append("paymentMethod", $("#payment_method option:selected").text());
        formData.append("tanggalPembayaran", $("#payment_date").val());

        $(".no_bukti_pembayaran").attr("readonly", true);

        $.ajax({
            url: "<?= base_url("pembayaran-po-lokal-bp/generate-no-pembayaran"); ?>",
            method: "POST",
            data: formData,
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                $(".no_bukti_pembayaran").val(response.paymentNo);
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan pada sistem',
                    text: 'Gagal menghasilkan nomor pembayaran otomatis',
                    confirmButtonColor: '#4e73df',
                });
                $(".no_bukti_pembayaran").attr("readonly", false);
            }
        });
    }
</script>

<?= $this->endSection(); ?>