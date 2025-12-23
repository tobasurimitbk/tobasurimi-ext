<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran Invoice Proforma Invoice" : "Tambah Pembayaran Invoice Proforma Invoice" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-proforma-invoice"); ?>">
                Kembali
            </a>

            <?php if (!empty($detail)) : ?>
                <?php if ($detail['status_posting'] == "0") : ?>
                    <?php if (can('Transaksi Internasional', 'Pembayaran Proforma Invoice', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Transaksi Internasional', 'Pembayaran Proforma Invoice', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Transaksi Internasional', 'Pembayaran Proforma Invoice', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Update
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else : ?>
                <?php if (can('Transaksi Internasional', 'Pembayaran Proforma Invoice', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create') ?>">Pembayaran Invoice Lokal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-ekspor') ?>">Pembayaran Invoice Ekspor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Pembayaran Proforma Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-lain') ?>">Pembayaran Invoice Lain Lain</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-return') ?>">Pembayaran Return</a>
                </li>
            </ul> -->
            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['id']) : ""; ?>">
                <input autocomplete="one-time-code" type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="<?= !empty($detail) ? $detail['type_invoice'] : "PROFORMA INVOICE"; ?>">

                <input type="hidden" name="tanda_terima_faktur_id" class="tanda_terima_faktur_id" value="">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required <?= !empty($detail) ? 'disabled value="' . $detail['no_pembayaran'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99;  margin-left: -30px; <?= !empty($detail) ? 'display:none;' : '' ?>" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> value="<?= !empty($detail) ? date('d/m/Y', strtotime($detail['tanggal'])) : '' ?>">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select no_dokumen" name="no_dokumen" id="no_dokumen" 
                                    <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : '' ?>>
                                <option value=""></option>
                                <?php if (!empty($dokumenList)): ?>
                                    <?php foreach ($dokumenList as $l): ?>
                                        <option <?= (!empty($detail)) ? (($detail['invoice_id']) == $l['id'] ? "selected" : "") : '' ?> 
                                                value="<?= encrypt($l['id']); ?>">
                                            <?= $l['no_pi']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No Dokumen PI</label>
                        </div>
                    </div>
                                        
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> class="form-select payment_term" name="payment_term" id="payment_term">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No Dokumen Payment TERM</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($divisi)): ?>
                                    <?php foreach ($divisi as $d): ?>
                                        <option <?= (!empty($detail)) ? (($detail['divisi_id']) == $d['id'] ? "selected" : "") : '' ?>  value="<?= $d['id']; ?>"><?= $d['divisi']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly name="customer" id="customer" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['customer_name'] : "" ?>" type="text" class="form-control customer" placeholder="Customer">
                            <label for="floatingInput">Customer</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input readonly name="valas" id="valas" autocomplete="one-time-code" value="<?= (!empty($detail)) ? $detail['valas_id'] : "" ?>" type="text" class="form-control valas" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Valas</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input name="kurs" id="kurs" autocomplete="one-time-code kurs" value="0,00" type="text" class="form-control kurs" placeholder="Pembayaran Oleh" onchange="this.value = formatRupiah2(this.value)">
                            <label for="floatingInput">Kurs</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">

                            <select
                                class="form-select payment_methods"
                                name="payment_methods"
                                id="payment_methods"
                                <?= !empty($detail) && $detail['status_posting'] == 1 ? 'disabled' : '' ?>
                            >
                                <option value=""></option>

                                <option
                                    value="BANK"
                                    <?= !empty($detail) ? (strtoupper($detail['payment_method']) == 'BANK' ? 'selected' : '') : '' ?>
                                >
                                    BANK
                                </option>

                                <option
                                    value="CASH"
                                    <?= !empty($detail) ? (strtoupper($detail['payment_method']) == 'CASH' ? 'selected' : '') : '' ?>
                                >
                                    CASH
                                </option>
                            </select>

                            <label for="payment_methods" style="z-index: 1;">Payment Methods</label>
                        </div>

                        <?php if (!empty($detail) && $detail['status_posting'] == 1): ?>
                            <input type="hidden" name="payment_methods" value="<?= strtoupper($detail['payment_method']) ?>">
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""; ?> name="bank_id" id="bank_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($bankList as $b) : ?>
                                    <option <?= !empty($detail) ? ($detail['bank_id'] == $b->id ? 'selected' : '') : '' ?> value="<?= $b->id ?>"><?= strtoupper($b->kode_bank) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kode Bank (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> name="pembayaran_oleh" id="pembayaran_oleh" autocomplete="one-time-code" value="<?= session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> class="form-select" name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= (!empty($detail)) ?  (($detail['akun_kas']) == $subs->id ? "selected" : "") : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> class="form-select" name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= (!empty($detail)) ?  (($detail['akun_selisih']) == $subs->id ? "selected" : "") : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'readonly' : '') : ""  ?> autocomplete="one-time-code" class="form-control keterangan" id="keterangan" name="keterangan"><?= !empty($detail) ? $detail['keterangan'] : "" ?></textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                </div>
                <div class="row">
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="text-align: center;">Nama Barang</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">Harga Satuan</th>
                                        <th style="text-align: center;">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody id="body-table" style="text-align: center;">
                                    <tr style="color: whitesmoke;">
                                        <td colspan="10">Tidak Ada Pembayaran</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    $(document).ready(function() {

        initializeEditMode();

        var validator = $(".create-form").validate({
            rules: {
                no_bukti_pembayaran: {
                    required: true
                },
                payment_date: {
                    required: true
                },
                jenis_dokumen: {
                    required: true
                },
                divisi_id: {
                    required: true
                },
                no_dokumen: {
                    required: true
                },
                customer: {
                    required: true
                },
                valas: {
                    required: true
                },
                kurs: {
                    required: true
                },
                payment_methods: {
                    required: true
                },
                pembayaran_oleh: {
                    required: true
                },
                akun_kas: {
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
                jenis_dokumen: {
                    required: "Jenis Dokumen wajib diisi"
                },
                divisi_id: {
                    required: "Department wajib diisi"
                },
                no_dokumen: {
                    required: "No Dokumen PI wajib diisi"
                },
                customer: {
                    required: "Customer wajib diisi"
                },
                valas: {
                    required: "Valas wajib diisi"
                },
                kurs: {
                    required: "Kurs wajib diisi"
                },
                payment_methods: {
                    required: "Payment Method wajib diisi"
                },
                pembayaran_oleh: {
                    required: "Pembayaran Oleh wajib diisi"
                },
                akun_kas: {
                    required: "Debit wajib diisi"
                },


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

        $("#payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        }).change(function() {
            let value = document.getElementById('auto_generate').checked ? true : false;
            if (value) {
                generatePaymentNumber();
            } else {
                $(".no_bukti_pembayaran").attr("readonly", false);
                $(".no_bukti_pembayaran").val("");
            }
        });

        $('#jenis_dokumen').select2({
            placeholder: "Pilih Jenis Dokumen",
            theme: "bootstrap-5"
        }).change(function() {
            getNoDokumen();
        });

        $('#akun_kas').select2({
            placeholder: "Akun Debit",
            theme: "bootstrap-5"
        });

        $('#akun_selisih').select2({
            placeholder: "Akun Selisih (Opsional)",
            theme: "bootstrap-5"
        });

        $('#bank_id').select2({
            placeholder: "Pilih Kode Bank",
            theme: "bootstrap-5"
        }).change(function() {
            let value = document.getElementById('auto_generate').checked ? true : false;
            if (value) {
                generatePaymentNumber();
            } else {
                $(".no_bukti_pembayaran").attr("readonly", false);
                $(".no_bukti_pembayaran").val("");
            }
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            let value = document.getElementById('auto_generate').checked ? true : false;
            if (value) {
                generatePaymentNumber();
            } else {
                $(".no_bukti_pembayaran").attr("readonly", false);
                $(".no_bukti_pembayaran").val("");
            }
        });

        $('#no_dokumen').select2({
            placeholder: "Pilih Nomor Dokumen",
            theme: "bootstrap-5",
            allowClear: true,
            disabled: <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'true' : 'false') : 'false' ?>
        }).on('change', function() {
            var selectedId = $(this).val();
            
            // Reset dropdown payment_term
            $('#payment_term').empty().append('<option value="">Loading...</option>');
            $('#payment_term').prop('disabled', true);
            
            if (selectedId) {
                $.ajax({
                    url: '<?= base_url("pembayaran-proforma-invoice/get-detail-data-proforma-invoice/id") ?>/' + selectedId,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        getValas();
                        drawTable(response.data);
                        getCustomer();

                        const term = response.data.dataProformaTerms;
                        const $paymentTerm = $('#payment_term');

                        // Reset dropdown payment_term
                        $paymentTerm.empty().append('<option value=""></option>');

                        if (term && term.id) {
                            $paymentTerm.append(
                                `<option value="${term.id}">${term.payment_term}</option>`
                            );
                        }

                        // Enable + init select2 untuk payment_term
                        $paymentTerm.prop('disabled', false).select2({
                            placeholder: "Pilih Payment Term",
                            theme: "bootstrap-5",
                            allowClear: true,
                            width: '100%',
                            disabled: <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'true' : 'false') : 'false' ?>
                        });

                        // Auto select payment term jika ada
                        $paymentTerm.val(term.id).trigger('change');
                        
                        // Trigger change event untuk payment_term jika diperlukan
                        if (term && term.id) {
                            setTimeout(function() {
                                $paymentTerm.trigger('change');
                            }, 100);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading PI details:', error);
                        $('#payment_term').empty().append('<option value="">Error loading data</option>');
                    }
                });
            } else {
                // Reset semua jika tidak ada dokumen terpilih
                $('#payment_term').empty().append('<option value=""></option>');
                $('#payment_term').prop('disabled', true);
                // Trigger re-initialization
                $('#payment_term').select2({
                    placeholder: "Pilih Payment Term",
                    theme: "bootstrap-5",
                    allowClear: true,
                    disabled: true
                });
            }
        });

        // Inisialisasi awal untuk payment_term
        $('#payment_term').select2({
            placeholder: "Pilih Payment Term",
            theme: "bootstrap-5",
            allowClear: true,
            disabled: <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'true' : 'false') : 'false' ?>
        }).on('change', function() {
            var selectedId = $(this).val();
            
            if (selectedId) {
                // Lakukan sesuatu jika payment term berubah
                // Misalnya: load detail payment term
                console.log('Payment term changed to:', selectedId);
            }
        });

        // Fungsi untuk menangani mode edit - panggil saat dokumen sudah terpilih
        function initializeEditMode() {
            <?php if (!empty($detail) && !empty($detail['invoice_id'])): ?>
            // Simulasikan change event untuk no_dokumen yang sudah terpilih
            var selectedId = $('#no_dokumen').val();
            if (selectedId) {
                // Trigger change event untuk load data yang sudah dipilih
                setTimeout(function() {
                    $('#no_dokumen').trigger('change');
                }, 500);
            }
            <?php endif; ?>
        }

        $('#payment_methods').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            let value = document.getElementById('auto_generate').checked ? true : false;
            if (value) {
                generatePaymentNumber();
            } else {
                $(".no_bukti_pembayaran").attr("readonly", false);
                $(".no_bukti_pembayaran").val("");
            }
        });

        // Ganti bagian AJAX submit di view:
        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            if ($(".create-form").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: id ? 'Update Data?' : 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: id ? 'Update' : 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.querySelector(".create-form");
                        const data = new FormData(form);
                        
                        // Format values untuk backend
                        formatDataForSubmit(data);
                        
                        // Determine URL
                        let url = id ? "<?= base_url('/pembayaran-proforma-invoice/update'); ?>" : "<?= base_url('pembayaran-invoice/save'); ?>";

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
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        window.location.href = `<?= base_url("pembayaran-proforma-invoice"); ?>`;
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi kesalahan pada sistem',
                                    text: xhr.responseText || error,
                                    confirmButtonColor: '#4e73df',
                                });
                            }
                        });
                    }
                });
            }
        });
    })


    // Helper function untuk format data
    function formatDataForSubmit(formData) {
        // Format nilai numerik untuk backend
        const formatNumber = (value) => {
            if (!value) return "0";
            return value.toString()
                .replace(/\./g, '')
                .replace(',', '.');
        };

        // Format semua field numerik
        const numericFields = ['total_bayar', 'potongan', 'kurs', 'total_amount_invoice'];
        numericFields.forEach(field => {
            const value = $(`.${field}`).val() || $(`#${field}`).val() || $('[name="' + field + '"]').val();
            if (value) {
                formData.set(field, formatNumber(value));
            }
        });

        // Pastikan required fields ada
        const requiredFields = ['no_dokumen', 'divisi_id', 'payment_methods'];
        requiredFields.forEach(field => {
            if (!formData.get(field)) {
                const value = $(`#${field}`).val();
                if (value) formData.set(field, value);
            }
        });
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
                    url: "<?= base_url("pembayaran-invoice/posting"); ?>",
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

    function generatePaymentNumber() {
            // Get selected divisi and bank values
            let id = $("#id").val();
            let noTransaksi = $("#no_bukti_pembayaran").val();
            let jenisPembayaran = "MERAH";
            let divisiId = $("#divisi_id option:selected").text();
            let divisiIdInt = $("#divisi_id option:selected").val();
            let bankId = $("#bank_id option:selected").val();
            let paymentMethod = $("#payment_method option:selected").val();
            let tanggalPembayaran = $("#payment_date").val();

            // Only generate if this is a new record (empty detail)
                const csrfToken = '<?= csrf_token() ?>';
                const csrf = $(`[name="${csrfToken}"]`);

                // Build URL with query parameters
                let url = "<?= base_url('pembayaran-po-lokal-bb/generate-no-pembayaran'); ?>";
                url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&paymentMethod=${encodeURIComponent(paymentMethod)}&divisiId=${encodeURIComponent(divisiId)}&bankId=${encodeURIComponent(bankId)}&tanggalPembayaran=${encodeURIComponent(tanggalPembayaran)}&id=${encodeURIComponent(id)}&noTransaksi=${encodeURIComponent(noTransaksi)}&divisiIdInt=${encodeURIComponent(divisiIdInt)}`;

                // Additional data if needed
                var formData = new FormData();
                formData.append("type", "Bahan Penolong");
                formData.append("payment_date", $("#payment_date").val());

                $(".no_bukti_pembayaran").attr("readonly", true);

                $.ajax({
                    url: url,
                    method: "GET",
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);

        if (value) {
            $(".no_bukti_pembayaran").attr("readonly", true);
            $.ajax({
                url: "<?= base_url("pembayaran-invoice/generate-no-pembayaran"); ?>",
                method: "POST",
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
                onError: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan pada sistem',
                        confirmButtonColor: '#4e73df',
                    });
                }
            });
        } else {
            $(".no_bukti_pembayaran").attr("readonly", false);
            $(".no_bukti_pembayaran").val("");
        }
    }

    function formatRupiah2(angka) {
        if (angka === null) {
            angka = 0;
        }

        angka = angka.toString();
        angka = angka.replace(/\./g, ',');
        angka = angka.replace(/[^\d,]/g, '');
        var parts = angka.split(',');
        var ribuan = parts[0];
        var desimal = parts[1] || '00';
        var reverse = ribuan.toString().split('').reverse().join('');
        var ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');
        return "" + ribuanFormatted + ',' + desimal;
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
        if (destroyFormatRupiah(numericValue) < 0 || isNaN(destroyFormatRupiah(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }
        if (numericValue > maxAmount) {
            input.value = maxAmount;
        }
    }

    function getValas() {
        var dokumen_id = $("#no_dokumen").val();
        $.ajax({
            url: "<?= base_url("pembayaran-invoice/get-valas-sales-ekspor-pi"); ?>",
            method: "GET",
            dataSrc: "data",
            data: {
                id: dokumen_id
            },
            beforeSend: function(xhr) {
                setLoading();
                // xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                $("#valas").val("");
                $("#valas").val(res);
            }
        })
    }

    // Fungsi drawTable yang diperbaiki untuk edit mode
    function drawTable(data, isEditMode = false, existingPayment = null) {
        const table = $('#dataTable');
        table.find('tbody').empty();

        let total_amount = 0;

        // Hitung total dari barang
        data.dataPIBarang.forEach(function(barang) {
            barang.size_breakdown.forEach(function(detail) {
                let row = $('<tr style="color:whitesmoke;">');
                row.append(`<td style="text-align:center;">${barang.nama_barang}</td>`);
                row.append(`<td style="text-align:center;">${detail.qty} ${detail.satuan_size_code}</td>`);
                row.append(`<td style="text-align:center;">${greatFormatRupiah(detail.harga)}</td>`);
                row.append(`<td style="text-align:center;">${greatFormatRupiah(detail.total)}</td>`);
                
                table.find('tbody').append(row);
                total_amount += parseFloat(detail.total);
            });
        });

        // Ambil nilai dari existing payment jika edit mode
        let existingPotongan = 0;
        let existingTotalBayar = 0;
        let existingKurs = 1;

        if (isEditMode && existingPayment) {
            existingPotongan = parseFloat(existingPayment.potongan || 0);
            existingTotalBayar = parseFloat(existingPayment.total_bayar || 0);
            existingKurs = parseFloat(existingPayment.kurs || 1);
        }

        // Set kurs field
        $('#kurs').val(greatFormatRupiah(existingKurs));

        // Hitung total sudah dibayar (kecuali payment yang sedang diedit)
        let totalSudahBayar = parseFloat(data.dataPI.total_bayar || 0);
        let totalSudahBayarFix = isEditMode
            ? totalSudahBayar - existingTotalBayar
            : totalSudahBayar;

        totalSudahBayarFix = Math.max(totalSudahBayarFix, 0);

        // Hitung sisa
        let sisaSebelumPotongan = Math.max(total_amount - totalSudahBayarFix, 0);
        let sisaSetelahPotongan = Math.max(sisaSebelumPotongan - existingPotongan, 0);

        // Tampilkan summary
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="3" style="text-align:right;">Total Harga Barang</td>
                <td style="text-align:center;">${greatFormatRupiah(total_amount)}</td>
            </tr>
            <tr style="color:yellow;font-weight:bold;">
                <td colspan="3" style="text-align:right;">Grand Total</td>
                <td style="text-align:center;">${greatFormatRupiah(total_amount)}</td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="3" style="text-align:right;">Total Sudah Dibayar</td>
                <td style="text-align:center;">${greatFormatRupiah(totalSudahBayarFix)}</td>
            </tr>
            <tr style="color:whitesmoke;font-weight:bold;">
                <td colspan="3" style="text-align:right;">Sisa Pembayaran</td>
                <td class="total_amount_invoice" style="text-align:center;" data-value="${sisaSetelahPotongan}">
                    ${greatFormatRupiah(sisaSetelahPotongan)}
                </td>
            </tr>
        `);

        // Input potongan
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="3" style="text-align:right;">Potongan</td>
                <td style="text-align:center;">
                    <input
                        type="text"
                        name="potongan"
                        class="form-control potongan trigger-input"
                        value="${greatFormatRupiah(existingPotongan)}"
                        data-max="${sisaSebelumPotongan}"
                        ${data.dataPI.status_bayar == 1 ? 'readonly' : ''}
                        oninput="limitInputBayar(this, ${sisaSebelumPotongan})"
                        onchange="this.value = greatFormatRupiah(this.value); calculateRemaining();"
                    >
                </td>
            </tr>
        `);

        // Input total bayar
        let totalBayarDefault = isEditMode ? existingTotalBayar : sisaSetelahPotongan;
        
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="3" style="text-align:right;">Anda Membayar Sebesar</td>
                <td style="text-align:center;">
                    <input
                        type="text"
                        name="total_bayar"
                        class="form-control total-bayar trigger-input"
                        value="${greatFormatRupiah(totalBayarDefault)}"
                        data-max="${sisaSetelahPotongan}"
                        ${data.dataPI.status_bayar == 1 ? 'readonly' : ''}
                        onchange="this.value = greatFormatRupiah(this.value); validatePayment();"
                        oninput="validatePayment()"
                    >
                </td>
            </tr>
        `);

        // Event handler untuk real-time calculation
        $(document).off('input.updatePayment').on('input.updatePayment', '.potongan, .total-bayar', function() {
            calculateRemaining();
        });
    }

    // Fungsi validasi pembayaran
    function validatePayment() {
        let potongan = destroyFormatRupiah($('.potongan').val()) || 0;
        let totalBayar = destroyFormatRupiah($('.total-bayar').val()) || 0;
        let sisaElement = $('.total_amount_invoice');
        let maxBayar = parseFloat(sisaElement.data('value') || sisaElement.text().replace(/\./g, '').replace(',', '.'));
        
        if (totalBayar > maxBayar) {
            Swal.fire({
                icon: 'warning',
                title: 'Pembayaran Melebihi Sisa',
                text: `Maksimal pembayaran: ${greatFormatRupiah(maxBayar)}`,
                confirmButtonColor: '#4e73df',
            });
            $('.total-bayar').val(greatFormatRupiah(maxBayar));
        }
    }

    // Fungsi hitung sisa
    function calculateRemaining() {
        let potongan = destroyFormatRupiah($('.potongan').val()) || 0;
        let totalElement = $('.total_amount_invoice');
        let totalText = totalElement.text().replace(/\./g, '').replace(',', '.');
        let totalValue = parseFloat(totalText) || 0;
        
        // Update sisa setelah potongan
        let sisaSetelahPotongan = Math.max(totalValue - potongan, 0);
        totalElement.text(greatFormatRupiah(sisaSetelahPotongan));
        totalElement.data('value', sisaSetelahPotongan);
        
        // Update max untuk total bayar
        $('.total-bayar').data('max', sisaSetelahPotongan);
    }

    function getCustomer() {
        let invoice_id = $("#no_dokumen").val();
        let type_invoice = $("#tipe_invoice").val();

        if (invoice_id && type_invoice) {
            $.ajax({
                url: '<?= base_url('pembayaran-invoice/get-customer') ?>',
                method: "GET",
                data: {
                    invoice_id: invoice_id,
                    type_invoice: type_invoice
                },
                dataType: "json",
                success: function(res) {
                    $('#customer').val(res).change();
                }
            })
        }
    }
</script>

<?= $this->endSection(); ?>