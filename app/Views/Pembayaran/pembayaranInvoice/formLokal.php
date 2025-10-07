<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran Invoice Lokal" : "Tambah Pembayaran Invoice Lokal" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-invoice"); ?>">
                Kembali
            </a>

            <?php if (!empty($detail)) : ?>
                <?php if ($detail['status_posting'] == "0") : ?>
                    <?php if (can('Pembayaran', 'Pembayaran Invoice', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Pembayaran Invoice', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Pembayaran Invoice', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Update
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

            <?php else : ?>
                <?php if (can('Pembayaran', 'Lokal BP', 'c')) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-form">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Penerimaan Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-ekspor') ?>">Pembayaran Invoice Ekspor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-proforma-invoice') ?>">Pembayaran Proforma Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-lain') ?>">Pembayaran Invoice Lain Lain</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-return') ?>">Pembayaran Return</a>
                </li>
            </ul>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['id']) : ""; ?>">
                <input autocomplete="one-time-code" type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="<?= !empty($detail) ? encrypt($detail['type_invoice']) : "LOKAL"; ?>">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : '' ?> autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required <?= !empty($detail) ? 'value="' . $detail['no_pembayaran'] . '"' : '' ?>>
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
                                <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> value="<?= !empty($detail) ? date('d/m/Y', strtotime($detail['tanggal'])) : '' ?>">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select divisi_id " name="divisi_id" id="divisi_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d): ?>
                                    <option <?= !empty($detail) ? ($detail['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select customer" name="customer" id="customer">
                                <option value=""></option>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $cus): ?>
                                        <option <?= !empty($detail) ?  (encrypt($detail['customer_id']) == $cus['id'] ? "selected" : "") : '' ?> value="<?= encrypt($cus['id']); ?>"><?= $cus['name']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : "" ?>
                                class="form-select no_dokumen"
                                name="no_dokumen[]"
                                id="no_dokumen"
                                multiple>
                            </select>
                            <label for="no_dokumen" style="z-index: 1;">No Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select payment_methods " name="payment_methods" id="payment_methods">
                                <option value=""></option>
                                <option <?= !empty($detail) ? ($detail['payment_method'] == 'bank' ? 'selected' : '') : '' ?> value="BANK">BANK</option>
                                <option <?= !empty($detail) ? ($detail['payment_method'] == 'cash' ? 'selected' : '') : '' ?> value="CASH">CASH</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Payment Methods</label>
                        </div>
                    </div>

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
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="pembayaran_dari" id="pembayaran_dari" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaran_dari'] : '' ?>" type="text" class="form-control" placeholder="Pembayaran Dari">
                            <label for="floatingInput">Pembayaran Dari</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_kas" id="akun_kas">
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
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_selisih" id="akun_selisih">
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
                            <textarea <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : "" ?> 
                                    autocomplete="one-time-code" 
                                    class="form-control keterangan" 
                                    id="keterangan" 
                                    name="keterangan" 
                                    style="height: 90px;"><?= !empty($detail) ? $detail['keterangan'] : '' ?></textarea>
                            <label for="keterangan">Keterangan</label>
                        </div>

                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap " id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr style="color: whitesmoke;">
                                    <th style="text-align: center;">No Invoice</th>
                                    <th style="text-align: center;">Kode Barang</th>
                                    <th style="text-align: center;">Nama Barang / Invoice</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Harga Satuan</th>
                                    <th style="text-align: center;">Sub Total</th>
                                    <th style="text-align: center;">Total Sudah Bayar</th>
                                    <th style="text-align: center;">Total Sisa Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">
                                <?php if (empty($detail)):  ?>
                                    <tr style="color: whitesmoke;">
                                        <td colspan="9" style="text-align: center;">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var dataList = [];
    var totalPembayaran = 0;
    var sisaPayForKeteranganCondition = 0;

    $(document).ready(function() {

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
                divisi_id: {
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
                pembayaran_dari: {
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
                    required: "No Dokumen wajib diisi"
                },
                customer: {
                    required: "Customer wajib di pilih"
                },
                divisi_id: {
                    required: "Departemen Wajib Diisi"
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
                pembayaran_dari: {
                    required: "Pembayaran Dari wajib diisi"
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

        $('#customer').select2({
            placeholder: "Pilih Customer",
            theme: "bootstrap-5"
        }).change(function() {
            const table = $('#dataTable');
            table.find('tbody').empty();
            let customerId = $(this).val();
            getDataDokumenInvoice(customerId);
        });

        //CSS SELECT2 FLOATING LABEL
        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.form-select')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.form-select')
            .parent('div')
            .find('label')
            .css('z-index', '1');

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
        $('#akun_kas_lain').select2({
            placeholder: "Akun Debit Lain",
            theme: "bootstrap-5"
        });
        $('#akun_selisih_lain').select2({
            placeholder: "Akun Kredit Lain (Opsional)",
            theme: "bootstrap-5"
        });

        $('#bank_id').select2({
            placeholder: "Pilih kode bank",
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

        // Trigger event change jika sudah ada nilai default
        const selectedCustomerId = "<?= !empty($detail) ? encrypt($detail['customer_id']) : ''; ?>";
        if (selectedCustomerId) {
            $('#customer').val(selectedCustomerId).trigger('change'); // Trigger change secara manual
        }


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
            allowClear: false
        }).change(function() {
            const table = $('#dataTable');
            table.find('tbody').empty();
            getDataSalesLokal();
        });


        $('#payment_methods').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            let paymentMethod = $(this).val();
            if (paymentMethod === "BANK") {
                $('#bank_id').prop('disabled', false);
            } else {
                $('#bank_id').val(null).trigger('change');
                $('#bank_id').prop('disabled', true);
                let value = document.getElementById('auto_generate').checked ? true : false;
                if (value) {
                    generatePaymentNumber();
                } else {
                    $(".no_bukti_pembayaran").attr("readonly", false);
                    $(".no_bukti_pembayaran").val("");
                }
            }
        });



        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);



            if (id) {
                // UPDATE
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
                            const formData = new FormData(document.querySelector(".create-form"));

                            // Bangun ulang list_barang dan ambil input total per index
                            const cleanListBarang = dataList.map((item, index) => {
                                const totalBayarInput = $(`#sisa_bayar_${index}`);
                                const totalTagihanInput = $(`#total_amount_invoice_${index}`);

                                return {
                                    ...item,
                                    total_amount_invoice: destroyFormatRupiah(totalTagihanInput.val() || item.amount_invoice || 0),
                                    total_bayar: destroyFormatRupiah(totalBayarInput.val() || 0),
                                    harga_barang_invoice: destroyFormatRupiah(item.harga_barang_invoice),
                                    qty_invoice: destroyFormatRupiah(item.qty_invoice),
                                    keterangan_pajak: $(`#keterangan_pajak_${index}`).val() || '',
                                    nominal_pajak: destroyFormatRupiah($(`#nominal_pajak_${index}`).val() || 0),
                                };
                            });

                            // Masukkan ke FormData
                            formData.append('list_barang', JSON.stringify(cleanListBarang));
                            $.ajax({

                                url: "<?= base_url("/pembayaran-invoice/update"); ?>",
                                data: formData,
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
                                            })
                                            .then(() => {
                                                window.location.href = `<?= base_url("pembayaran-invoice"); ?>`;
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        });
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        }

                    })
                }

            } else {
                // CREATE
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
                            const formData = new FormData(document.querySelector(".create-form"));

                            // Bangun ulang list_barang dan ambil input total per index
                            const cleanListBarang = dataList.map((item, index) => {
                                const totalBayarInput = $(`#sisa_bayar_${index}`);
                                const totalTagihanInput = $(`#total_amount_invoice_${index}`);

                                return {
                                    ...item,
                                    total_amount_invoice: destroyFormatRupiah(totalTagihanInput.val() || item.amount_invoice || 0),
                                    total_bayar: destroyFormatRupiah(totalBayarInput.val() || 0),
                                    harga_barang_invoice: destroyFormatRupiah(item.harga_barang_invoice),
                                    qty_invoice: destroyFormatRupiah(item.qty_invoice),
                                    keterangan_pajak: $(`#keterangan_pajak_${index}`).val() || '',
                                    nominal_pajak: destroyFormatRupiah($(`#nominal_pajak_${index}`).val() || 0),
                                };
                            });

                            // Masukkan ke FormData
                            formData.append('list_barang', JSON.stringify(cleanListBarang));
                            $.ajax({
                                url: "<?= base_url("pembayaran-invoice/save"); ?>",
                                data: formData,
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
                                            })
                                            .then(() => {
                                                window.location.href = `<?= base_url("pembayaran-invoice"); ?>`;
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                }
                            });
                        }
                    })
                }
            }
        })
    })



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
                    url: "<?= base_url("pembayaran-invoice/delete"); ?>",
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
                            window.location.href = "<?= base_url('pembayaran-invoice') ?>"
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



    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            generatePaymentNumber();
        } else {
            $(".no_bukti_pembayaran").attr("readonly", false);
            $(".no_bukti_pembayaran").val("");
        }
    }

    function limitInputBayar(input, maxAmount) {
        var inputValue = input.value;

        // Hapus karakter non-numerik kecuali titik desimal
        var numericValue = inputValue.replace(/[^0-9.]/g, '');

        // Pastikan angka gak diawali nol atau hanya titik
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');

        // Validasi nilai angka: tidak boleh negatif atau NaN
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }

        // Batas maksimum
        if (parseFloat(numericValue) > maxAmount) {
            input.value = maxAmount;
        }
    }


    function generatePaymentNumber() {
            // Get selected divisi and bank values
            let jenisPembayaran = $("#jenis_pembayaran option:selected").text();
            let divisiId = $("#divisi_id option:selected").text();
            let bankId = $("#bank_id option:selected").val();
            let paymentMethod = $("#payment_method option:selected").val();
            let tanggalPembayaran = $("#payment_date").val();

            // Only generate if this is a new record (empty detail)
                const csrfToken = '<?= csrf_token() ?>';
                const csrf = $(`[name="${csrfToken}"]`);

                // Build URL with query parameters
                let url = "<?= base_url('pembayaran-po-lokal-bb/generate-no-pembayaran'); ?>";
                url += `?jenisPembayaran=${encodeURIComponent(jenisPembayaran)}&paymentMethod=${encodeURIComponent(paymentMethod)}&divisiId=${encodeURIComponent(divisiId)}&bankId=${encodeURIComponent(bankId)}&tanggalPembayaran=${encodeURIComponent(tanggalPembayaran)}`;

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



    function getNoDokumen() {
        var jenisDokumen = $('#jenis_dokumen').val();
        if (jenisDokumen !== "ekspor") {
            $("#valas").val("-");
            $("#kurs").prop('readonly', true);
        } else {
            $("#valas").val();
            $("#kurs").prop('readonly', false);
        }
        $.ajax({
            url: '<?= base_url('pembayaran-invoice/get-dokumen-list') ?>',
            method: "GET",
            data: {
                jenis_dokumen: jenisDokumen,
            },
            dataType: "json",
            success: function(res) {
                $(".no_dokumen").empty();
                $(".no_dokumen").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".no_dokumen").append(`<option value="${item}">${item}</option>`)
                })
            }
        })
    }


    function getDataDokumenInvoice(customerId) {
        var pembayaranInvoiceDetail = $('.id').val();
        let url = `<?= base_url("pembayaran-invoice/get-dokumen-invoice-lokal"); ?>/${customerId}`;
        if (pembayaranInvoiceDetail) {
            url += `?pembayaran_invoice_id=${pembayaranInvoiceDetail}`;
        }
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: function(res) {
                // Clear existing options
                $('#no_dokumen').empty();

                // Tambahkan opsi kosong jika diperlukan
                $('#no_dokumen').append('<option value=""></option>');

                // Iterasi hasil data dan tambahkan ke dropdown
                res.data.forEach(function(item) {
                    $('#no_dokumen').append(
                        `<option value="${item.id}">${item.no_faktur}</option>`
                    );
                });


                <?php if (isset($detail) && !empty($detail['invoice_id'])): ?>
                    const selectedIds = "<?= $detail['invoice_id']; ?>"
                        .replace(/[\[\]\'\s]/g, '') // Hapus tanda kutip, kurung siku, dan spasi
                        .split(','); // Split string menjadi array berdasarkan koma
                    // Set pilihan yang sudah ada pada dropdown
                    $('#no_dokumen').val(selectedIds).trigger('change'); // `val()` untuk set value, `trigger('change')` untuk trigger perubahan
                <?php else: ?>
                    console.log("Invoice ID tidak tersedia");
                <?php endif; ?>


            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function getDataSalesLokal() {
        const selectedIds = $("#no_dokumen").val(); // Ambil nilai array dari dropdown
        dataList = [];
        $.ajax({
            url: "<?= base_url('pembayaran-invoice/get-barang-sales-lokal'); ?>",
            method: "GET",
            data: {
                id: JSON.stringify(selectedIds), // Kirim sebagai string JSON
                pembayaran_invoice_id: "<?= !empty($detail) ? encrypt($detail['id']) : '' ?>"
            },
            dataType: "json",
            success: function(res) {
                if (res.status && res.data.length > 0) {
                    if (res.isImport) {
                        res.data.forEach((data) => {
                            dataList.push({
                                id: getID(),
                                qty_invoice: data.qty_invoice || "-",
                                document_type: data.document_type || "-",
                                harga_barang_invoice: data.harga_barang_invoice || null,
                                amount_invoice: data.amount_invoice || "-",
                                kode_barang: data.kode_barang || "-",
                                barang_name: data.barang_name || "-",
                                sales_order_invoice_id: data.sales_order_invoice_id || null,
                                sales_order_invoice_detail_id: data.sales_order_invoice_detail_id || null,
                                no_faktur: data.no_faktur || "-",
                                akun_kas_lain: data.id_akun_kas_lain || null,
                                akun_selisih_lain: data.id_akun_selisih_lain || null,
                                nama_akun_kas_lain: data.akun_kas_lain || "-",
                                nama_akun_selisih_lain: data.akun_selisih_lain || "-"
                            });
                        });
                    } else {
                        res.data.forEach((data) => {
                            dataList.push({
                                id: getID(),
                                qty_invoice: data.qty_invoice,
                                harga_barang_invoice: data.harga_barang_invoice,
                                amount_invoice: data.amount_invoice,
                                harga_dibayar: destroyFormatRupiah(data.harga_dibayar || 0),
                                sisa_bayar: destroyFormatRupiah(data.sisa_bayar || (data.amount_invoice - (data.harga_dibayar || 0))),
                                kode_barang: data.kode_barang,
                                barang_name: data.barang_name,
                                sales_order_invoice_id: data.sales_order_invoice_id,
                                sales_order_invoice_detail_id: data.sales_order_invoice_detail_id,
                                no_faktur: data.no_faktur || "-",
                                akun_kas_lain: data.id_akun_kas_lain || null,
                                akun_selisih_lain: data.id_akun_selisih_lain || null,
                                nama_akun_kas_lain: data.akun_kas_lain || "-",
                                nama_akun_selisih_lain: data.akun_selisih_lain || "-"
                            });
                        });
                    }
                }

                const totalPembayaran = parseFloat(res.totalPembayaran) || 0;
                const totalSudahDiBayar = parseFloat(res.totalSudahDiBayar) || 0;

                // Refresh tabel dengan data terbaru
                drawTable(dataList, totalPembayaran, totalSudahDiBayar);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    
    function drawTable(dataList, totalPembayaran = 0, totalSudahDiBayar = 0) {
        const table = $('#dataTable');
        table.find('tbody').empty();

        let globalIndex = 0;

        // Group data berdasarkan faktur dan nama barang
        const groupedData = dataList.reduce((acc, item) => {
            const key = `${item.no_faktur}-${item.barang_name}`;
            if (!acc[key]) acc[key] = [];
            acc[key].push(item);
            return acc;
        }, {});

        Object.keys(groupedData).forEach(key => {
            const group = groupedData[key];
            let isFirstRow = true;

            group.forEach(item => {
                const total = destroyFormatRupiah(item.amount_invoice || 0);
                const dp = destroyFormatRupiah(item.total_bayar_detail || item.harga_dibayar || 0);
                const sisa = destroyFormatRupiah(item.sisa_bayar || (total - dp));

                const newRow = $('<tr style="color:whitesmoke;">');

                // Kolom no faktur (merge rowspan)
                if (isFirstRow) {
                    newRow.append(
                        $('<td>', {
                            rowspan: group.length,
                            style: 'text-align:center;'
                        }).text(item.no_faktur)
                    );
                    isFirstRow = false;
                }

                newRow.append($('<td style="text-align:center;">').text(item.kode_barang));
                newRow.append($('<td style="text-align:center;">').text(item.barang_name));
                newRow.append($('<td style="text-align:center;">').text(item.qty_invoice));
                newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(item.harga_barang_invoice)));

                // Total tagihan (readonly)
                newRow.append(
                    $('<td style="text-align:center;">').append(
                        $('<input>', {
                            type: 'text',
                            name: 'total_amount_invoice[]',
                            id: 'total_amount_invoice_' + globalIndex,
                            class: 'form-control text-end total-amount-invoice',
                            readonly: true,
                            value: greatFormatRupiah(total)
                        })
                    )
                );

                // Total dibayar sebelumnya (readonly)
                newRow.append(
                    $('<td style="text-align:center;">').append(
                        $('<input>', {
                            type: 'text',
                            name: 'total_bayar[]',
                            id: 'total_bayar_' + globalIndex,
                            class: 'form-control text-end total-bayar',
                            readonly: true,
                            value: greatFormatRupiah(dp)
                        })
                    )
                );

                // Sisa bayar sekarang (editable)
                const sisaInput = $('<input>', {
                    type: 'text',
                    name: 'sisa_bayar[]',
                    id: 'sisa_bayar_' + globalIndex,
                    class: 'form-control text-end sisa-bayar',
                    value: greatFormatRupiah(sisa)
                });

                sisaInput.on('keyup', function () {
                    this.value = greatFormatRupiah(this.value);
                    updateTotalSummary();
                });

                newRow.append($('<td style="text-align:center;">').append(sisaInput));

                table.find('tbody').append(newRow);
                globalIndex++;
            });
        });

        addSummaryRows(table);
        updateTotalSummary();
    }

    // Tambahkan summary total di bawah
    function addSummaryRows(table) {
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="7" style="text-align: right;">Total Tagihan</td>
                <td class="total_tagihan text-center"></td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="7" style="text-align: right;">Total Sudah Dibayar (DP)</td>
                <td class="total_dibayar text-center"></td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="7" style="text-align: right;">Total Pembayaran Sekarang</td>
                <td class="sisa_total text-center"></td>
            </tr>
        `);
    }

    // Hitung ulang summary bawah
    function updateTotalSummary() {
        let totalTagihan = 0;
        let totalDP = 0;
        let totalSisaBayar = 0;

        $('.total-amount-invoice').each(function () {
            totalTagihan += destroyFormatRupiah($(this).val() || 0);
        });

        $('.total-bayar').each(function () {
            totalDP += destroyFormatRupiah($(this).val() || 0);
        });

        $('.sisa-bayar').each(function () {
            totalSisaBayar += destroyFormatRupiah($(this).val() || 0);
        });

        $('.total_tagihan').text(greatFormatRupiah(totalTagihan));
        $('.total_dibayar').text(greatFormatRupiah(totalDP));
        $('.sisa_total').text(greatFormatRupiah(totalSisaBayar));
    }


    // 🔥 Fungsi baru buat hitung ulang total
    // function updateTotal(table, totalPembayaran, totalSudahDiBayar) {
    //     let totalInput = 0;

    //     // Loop semua input total_bayar
    //     table.find('input.total-bayar').each(function() {
    //         let rawValue = $(this).val().replace(/[^0-9]/g, '');
    //         let val = parseFloat(rawValue) || 0;
    //         totalInput += val;
    //     });

    //     // Update tampilan total
    //     table.find('.total_pembayaran').text(greatFormatRupiah(totalInput));

    //     // Hitung sisa pembayaran
    //     let sisa = totalInput - totalPembayaran;
    //     table.find('.sisa_pembayaran').text(greatFormatRupiah(sisa));

    //     // Optional: validasi batas maksimal
    //     if (totalInput < 0) totalInput = 0;
    // }


    function deleteBarang(id) {
        var indexToRemove = -1;
        for (let i = 0; i < dataList.length; i++) {
            if (dataList[i].id == id) {
                indexToRemove = i;
                break;
            }
        }

        if (indexToRemove !== -1) {
            dataList.splice(indexToRemove, 1);
            drawTable(dataList)
        }
    }

    // function getCustomer() {
    //     let invoice_id = $("#no_dokumen").val();
    //     let type_invoice = $("#tipe_invoice").val();

    //     $.ajax({
    //         url: '<?= base_url('pembayaran-invoice/get-customer') ?>',
    //         method: "GET",
    //         data: {
    //             invoice_id: invoice_id,
    //             type_invoice: type_invoice
    //         },
    //         dataType: "json",
    //         success: function(res) {
    //             $('#customer').val(res);
    //         }
    //     })
    // }

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };

    function updateKeterangan(totalTagihan, totalPembayaran) {
        const noDokumenElement = document.getElementById('no_dokumen');
        const customerElement = document.getElementById('customer');
        const textareaElement = document.getElementById('keterangan');

        // Ambil semua input total_bayar[] dan hitung totalnya
        let totalBayar = 0;
        $('input[name="total_bayar[]"]').each(function() {
            const val = $(this).val() || '0';
            totalBayar += destroyFormatRupiah(val);
        });

        // Nilai fallback untuk variabel undefined
        totalTagihan = isNaN(totalTagihan) ? 0 : totalTagihan;
        totalPembayaran = isNaN(totalPembayaran) ? 0 : totalPembayaran;

        // Hitung sisa pembayaran
        const sisa = totalTagihan - totalBayar;

        // Ambil customer & dokumen terpilih
        const selectedNoDokumen = Array.from(noDokumenElement.selectedOptions).map(option => option.text.trim());
        const selectedCustomer = Array.from(customerElement.selectedOptions).map(option => option.text.trim());

        // Tentukan prefix berdasarkan kondisi logis
        let prefix = "";

        if (totalBayar >= totalTagihan && totalTagihan > 0) {
            prefix = "TERIMA A/ INVOICE"; // Lunas
        } else if (totalBayar > 0 && totalBayar < totalTagihan && sisa > 0) {
            prefix = "TERIMA DP A/ INVOICE"; // Masih ada sisa
        } else if (sisa === 0 && totalBayar < totalTagihan) {
            prefix = "TERIMA PELUNASAN A/ INVOICE"; // Pelunasan
        } else {
            prefix = ""; // Default kosong
        }

        // Gabungkan hasil akhir ke textarea
        const combinedText = `${selectedCustomer[0] || ''}; ${prefix} ${selectedNoDokumen.join('; ')}`;
        textareaElement.value = combinedText.trim();
    }




    <?php if (!empty($detail)) : ?>
        getDataSalesLokal();

    <?php endif; ?>
</script>

<?= $this->endSection(); ?>