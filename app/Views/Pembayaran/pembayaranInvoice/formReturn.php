<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran Return Lokal" : "Tambah Pembayaran Return Lokal" ?></h1>
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
                <?php if (can('Pembayaran', 'Pembayaran Invoice', 'c')) : ?>
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
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create') ?>">Pembayaran Invoice Lokal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-ekspor') ?>">Pembayaran Invoice Ekspor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-lain') ?>">Pembayaran Invoice Lain Lain</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Pembayaran Return</a>
                </li>
            </ul>

            <div class="row mt-3">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['id']) : ""; ?>">
                <input autocomplete="one-time-code" type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="<?= !empty($detail) ? encrypt($detail['type_invoice']) : "RETURN"; ?>">

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

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select customer " name="customer" id="customer">
                                <option value=""></option>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $cus): ?>
                                        <option <?= (!empty($detail)) ?  (($detail['customer_id']) == $cus['id'] ? "selected" : "") : '' ?> value="<?= encrypt($cus['id']); ?>"><?= $cus['name']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Customer</label>
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
                                <option <?= !empty($detail) ? ($detail['payment_method'] == "bank" ? 'selected' : '')  : ""; ?> value="BANK">BANK</option>
                                <option <?= !empty($detail) ? ($detail['payment_method'] == "cash" ? 'selected' : '')  : ""; ?> value="CASH">CASH</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Payment Methods</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="pembayaran_oleh" id="pembayaran_oleh" autocomplete="one-time-code" value="<?= session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
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
                            <label for="floatingInput" style="z-index: 1;">Kredit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> autocomplete="one-time-code" class="form-control keterangan" id="keterangan" name="keterangan"><?= !empty($detail) ? $detail['keterangan'] : "" ?> </textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap " id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr style="color: whitesmoke;">
                                    <th style="text-align: center;">No Return</th>
                                    <th style="text-align: center;">Kode Barang</th>
                                    <th style="text-align: center;">Nama Barang</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Harga Satuan</th>
                                    <th style="text-align: center;">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                            </tbody>
                            <tfoot>
                                <!-- <tr style="color: whitesmoke;">
                                    <td colspan="9" style="text-align: center;color:black;">Tidak ada data</td>
                                </tr> -->
                            </tfoot>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    const subsAkuns = <?php echo json_encode($subsAkuns); ?>;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var dataList = [];
    var totalPembayaran = 0;

    <?php if (!empty($detail)) : ?>
        getBarangSalesReturn();
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
                akun_selisih: {
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
                akun_selisih: {
                    required: "Kredit wajib diisi"
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

        $('#akun_debit').select2({
            placeholder: "Akun Debit",
            theme: "bootstrap-5"
        });

        $('#akun_kredit').select2({
            placeholder: "Akun Kredit",
            theme: "bootstrap-5"
        });

        $('#akun_selisih').select2({
            placeholder: "Akun Selisih",
            theme: "bootstrap-5"
        });

        $('#akun_kas_lain').select2({
            placeholder: "Akun Debit Lain",
            theme: "bootstrap-5"
        });

        $('#akun_selisih_lain').select2({
            placeholder: "Akun Selisih Lain",
            theme: "bootstrap-5"
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });
        $('#no_dokumen').select2({
            placeholder: "Pilih Nomor Dokumen",
            theme: "bootstrap-5",
            allowClear: false
        }).change(function() {
            updateKeterangan();
            getBarangSalesReturn();
        })


        $('#payment_methods').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            allowClear: true
        });

        $('#customer').select2({
            placeholder: "Pilih Customer",
            theme: "bootstrap-5"
        }).change(function() {
            const table = $('#dataTable');
            table.find('tbody').empty();
            let customerId = $(this).val();
            updateKeterangan();
            getDataDokumenInvoice(customerId);
        });

        // Trigger event change jika sudah ada nilai default
        const selectedCustomerId = "<?= !empty($detail) ? encrypt($detail['customer_id']) : ''; ?>";
        if (selectedCustomerId) {
            $('#customer').val(selectedCustomerId).trigger('change'); // Trigger change secara manual
        }

        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
           
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
                                const cleanListBarang = dataList.map((item, index) => {
                                    const keteranganBarang = $(`.keteranganBarang[data-index="${index}"]`).val() || '';  
                                    const keteranganPajak = $(`.keteranganPajak[data-index="${index}"]`).val() || '';  
                                    const akunDebit = $(`.akun-debit[data-index="${index}"]`).val() || '';         
                                    const akunKredit = $(`.akun-kredit[data-index="${index}"]`).val() || '';       
                                    const pajakValue = destroyFormatRupiah($(`.pajak-input[data-index="${index}"]`).val()) || 0; 

                                    return {
                                        ...item,
                                        harga_barang_invoice: destroyFormatRupiah(item.harga_barang_return),  
                                        qty_invoice: item.qty_return,
                                        keterangan: keteranganBarang,
                                        keterangan_pajak: keteranganPajak,
                                        akun_debit: akunDebit,
                                        akun_kredit: akunKredit,
                                        nominal_pajak: pajakValue,
                                    };
                                });

                                const totalAmountInvoice = destroyFormatRupiah($(".total_amount_invoice").text()) || 0;
                                const totalBayar = destroyFormatRupiah($(".total-bayar").val()) || 0;
                                const formData = new FormData(document.querySelector(".create-form"));
                                formData.append('total_amount_invoice', totalAmountInvoice);
                                formData.append('total_bayar', totalBayar);
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
                                const cleanListBarang = dataList.map((item, index) => {
                                    const keteranganBarang = $(`.keteranganBarang[data-index="${index}"]`).val() || '';  
                                    const keteranganPajak = $(`.keteranganPajak[data-index="${index}"]`).val() || '';  
                                    const akunDebit = $(`.akun-debit[data-index="${index}"]`).val() || '';         
                                    const akunKredit = $(`.akun-kredit[data-index="${index}"]`).val() || '';       
                                    const pajakValue = destroyFormatRupiah($(`.pajak-input[data-index="${index}"]`).val()) || 0; 

                                    return {
                                        ...item,
                                        harga_barang_invoice: destroyFormatRupiah(item.harga_barang_return),  
                                        qty_invoice: item.qty_return,
                                        keterangan: keteranganBarang,
                                        keterangan_pajak: keteranganPajak,
                                        akun_debit: akunDebit,
                                        akun_kredit: akunKredit,
                                        nominal_pajak: pajakValue,
                                    };
                                });

                                const totalAmountInvoice = destroyFormatRupiah($(".total_amount_invoice").text()) || 0;
                                const totalBayar = destroyFormatRupiah($(".total-bayar").val()) || 0;

                                const formData = new FormData(document.querySelector(".create-form"));
                                formData.append('total_amount_invoice', totalAmountInvoice);
                                formData.append('total_bayar', totalBayar);
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
        let url = `<?= base_url("pembayaran-invoice/get-dokumen-invoice-return"); ?>/${customerId}`;
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
                        `<option value="${item.id}">${item.no_return}</option>`
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

    $(document).on('input', '#dataTable input[id^="keterangan_pajak"], #dataTable input[id^="nominal_pajak"]', function () {
        const row = $(this).closest('tr'); // Ambil baris tempat input berada
        const index = row.index(); // Dapatkan index baris
        const key = $(this).attr('id').split('_')[0]; // Ambil key berdasarkan nama input sebelum '_'
        const value = $(this).val(); // Ambil nilai input

        if (dataList[index]) {
            dataList[index][key] = value; // Perbarui dataList hanya untuk keterangan_pajak atau nominal_pajak
        }
    });

    function getBarangSalesReturn() {
        const arr = $('.no_dokumen').val();
        const pembayaranInvoiceId = "<?= !empty($detail) ? encrypt($detail['id']) : '' ?>";

        $.ajax({
            url: "<?= base_url('pembayaran-invoice/get-barang-sales-return'); ?>",
            method: "GET",
            data: {
                id: JSON.stringify(arr),
                pembayaran_invoice_id: pembayaranInvoiceId || null // Kirim null jika tidak ada ID
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    dataList = [];
                    if (res.data.length > 0) {
                        res.data.forEach((data) => {
                            dataList.push({
                                id: getID(),
                                qty_return: data.qty_return,
                                harga_barang_return: data.harga_barang_return,
                                amount_return: data.amount_return,
                                kode_barang: data.kode_barang,
                                barang_name: data.barang_name,
                                no_faktur: data.no_faktur || "",
                                keterangan: data.keterangan || "",
                                keterangan_pajak: data.keterangan_pajak || "",
                                nominal_pajak: data.nominal_pajak || "",
                                id_akun_kredit: data.id_akun_kredit || "",
                                id_akun_debit: data.id_akun_debit || "",
                            });
                        });
                    }

                    drawTable(dataList, res.totalPembayaran, res.totalSudahDiBayar || 0);
                }
            },
        });
    }

    function drawTable(dataList, totalPembayaran, totalSudahDiBayar) {
    const table = $('#dataTable');

    // Header tabel dengan kolom Keterangan setelah No Return
    table.find('thead').html(`
        <tr style="color: whitesmoke;">
            <th style="text-align: center;">No Return</th>
            <th style="text-align: center; width: 25%;">Keterangan</th>
            <th style="text-align: center; width: 10%;">Kode Barang</th>
            <th style="text-align: center; width: 15%;">Nama Barang</th>
            <th style="text-align: center; width: 7%;">Qty</th>
            <th style="text-align: center;">Harga Satuan</th>
            <th style="text-align: center;">Sub Total</th>
        </tr>
    `);
    table.find('tbody').empty();

    let total_amount = 0;

    $.each(dataList, function (index, item) {
        // Row untuk data barang
        let newRow = $('<tr class="data-row" style="color: whitesmoke;">');

        // Kolom data barang dengan kolom Keterangan setelah No Return
        newRow.append($('<td class="text-center">').text(item.no_faktur));

        // Input keterangan
        const keteranganBarang = $('<input type="text" class="form-control keteranganBarang" placeholder="Keterangan..." style="width: 100%;">')
            .attr('data-index', index)
            .val(item.keterangan || ""); // Pasang nilai dari response
        newRow.append($('<td class="text-center">').append(keteranganBarang));

        // Kolom lainnya
        newRow.append($('<td class="text-center">').text(item.kode_barang));
        newRow.append($('<td class="text-center">').text(item.barang_name));
        newRow.append($('<td class="text-center">').text(item.qty_return));
        newRow.append($('<td class="text-center">').text(greatFormatRupiah(item.harga_barang_return)));

        // Subtotal berdasarkan harga barang dan qty (tanpa pajak di sini)
        let subtotalValue = item.harga_barang_return * item.qty_return;
        const subtotalCell = $('<td class="text-center">').text(greatFormatRupiah(subtotalValue));

        // Update total amount
        total_amount += subtotalValue;

        // Append to the row
        newRow.append(subtotalCell);
        table.find('tbody').append(newRow);

        // Row untuk input tambahan (pajak, debit, kredit)
        let inputRow = $('<tr class="input-row" style="background-color: #f9f9f9; color: #333;">');

        inputRow.append(
            $('<td colspan="1">').text(
                "Pajak (" + item.no_faktur + " - " + item.kode_barang + "):"
            )
        );

        inputRow.append(
            $('<td colspan="2" class="text-left">').html(
                `<input type="text" data-index="${index}" class="form-control keteranganPajak" placeholder="Keterangan Pajak..." style="width: 100%;" value="${item.keterangan_pajak || ''}">`
            )
        );

        let debitOptions = '';
        let kreditOptions = '';
        $.each(subsAkuns, function (subIndex, subs) {
            debitOptions += `<option value="${subs.id}" ${item.id_akun_debit == subs.id ? 'selected' : ''}>${subs.no_sub} ${subs.nama_sub}</option>`;
            kreditOptions += `<option value="${subs.id}" ${item.id_akun_kredit == subs.id ? 'selected' : ''}>${subs.no_sub} ${subs.nama_sub}</option>`;
        });

        inputRow.append($('<td class="text-center">').html(
            `<div class="form-floating" style="height: 50px;">
                <select class="form-select akun-debit" data-index="${index}" id="akun_debit_${index}" name="akun_debit" style="width: 100%;">
                    <option disabled selected value=""></option>
                    ${debitOptions}
                </select>
            </div>`  
        ));

        inputRow.append($('<td class="text-center">').html(
            `<div class="form-floating" style="height: 50px;">
                <select class="form-select akun-kredit" data-index="${index}" id="akun_kredit_${index}" name="akun_kredit" style="width: 100%;">
                    <option disabled selected value=""></option>
                    ${kreditOptions}
                </select>
            </div>`
        ));

        // Pajak dan penghitungan subtotal baru setelah pajak
        const pajakInput = $('<input type="text" class="form-control pajak-input" placeholder="Nilai Pajak" style="width: 100%;">')
            .attr('data-index', index)
            .val(greatFormatRupiah(item.nominal_pajak || 0)) // Pasang nilai pajak dari response
            .appendTo(inputRow) // Pastikan elemen ditambahkan ke row sebelum listener
            .on('keyup', function () {
                let pajakValue = destroyFormatRupiah($(this).val()) || 0;
                if (pajakValue > subtotalValue) {
                    pajakValue = subtotalValue;
                }
                $(this).val(greatFormatRupiah(pajakValue));

                // Calculate new subtotal after tax
                const newSubtotal = Math.round((subtotalValue - pajakValue) * 100) / 100;
                subtotalCell.text(greatFormatRupiah(newSubtotal)); // Update subtotal cell with the new value
            });


        inputRow.append($('<td colspan="2" class="text-center">').append(pajakInput));
        table.find('tbody').append(inputRow);

        inputRow.find('.akun-debit').select2({
            placeholder: "Akun Debit",
            theme: "bootstrap-5"
        });

        inputRow.find('.akun-kredit').select2({
            placeholder: "Akun Kredit",
            theme: "bootstrap-5"
        });
    });

    // Add summary rows with the updated total amount
    addSummaryRows(
        table,
        total_amount,
        totalPembayaran,
        totalPembayaran - totalSudahDiBayar,
        totalSudahDiBayar
    );
}


    function addSummaryRows(table, total_amount, total_invoice, limit_bayar, totalSudahDiBayar) {
        // Pastikan semua parameter memiliki nilai default 0 jika undefined, null, atau NaN
        total_amount = isNaN(total_amount) ? 0 : total_amount;
        total_invoice = isNaN(total_invoice) ? 0 : total_invoice;
        limit_bayar = isNaN(limit_bayar) ? 0 : limit_bayar;
        totalSudahDiBayar = isNaN(totalSudahDiBayar) ? 0 : totalSudahDiBayar;

        // Tambahkan baris untuk Total Pembayaran, Total Sudah Dibayar, dan Sisa Pembayaran
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Total Pembayaran</td>
                <td colspan="2" style="text-align:center;">${greatFormatRupiah(total_amount)}</td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Total Sudah Dibayar</td>
                <td colspan="2" class="total_dibayar" style="text-align:center;">${greatFormatRupiah(total_invoice)}</td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Sisa Pembayaran</td>
                <td colspan="2" class="total_amount_invoice" style="text-align:center;">${greatFormatRupiah(total_amount - total_invoice)}</td>
            </tr>
        `);

        // Tambahkan baris untuk input Total Bayar
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Anda Membayar Sebesar</td>
                <td colspan="2" style="text-align:center;">
                    <input 
                        autocomplete="one-time-code" 
                        data-id="" 
                        class="form-control total-bayar trigger-input" 
                        type="text" 
                        value="" 
                        name="total_bayar"
                        placeholder="Masukkan jumlah pembayaran">
                </td>
            </tr>
        `);

        // Tambahkan event listener untuk validasi input
        table.find('input.total-bayar').on('change', function() {
            let rawValue = $(this).val(); // Ambil nilai input
            let cleanValue = rawValue.replace(/[^0-9]/g, ''); // Hapus karakter non-digit
            let numberValue = parseInt(cleanValue) || 0; // Konversi ke angka, default 0

            let sisaPembayaran = total_amount - total_invoice; // Hitung sisa pembayaran

            // Validasi nilai input
            if (numberValue > sisaPembayaran) {
                numberValue = sisaPembayaran; // Tidak boleh lebih besar dari sisa pembayaran
            } else if (numberValue < 0) {
                numberValue = 0; // Tidak boleh kurang dari 0
            }

            // Tampilkan nilai yang sudah divalidasi dalam format Rupiah
            $(this).val(greatFormatRupiah(numberValue));
        });
    }


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

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };

    function updateKeterangan() {
        // Ambil elemen <select> dan <textarea>
        const noDokumenElement = document.getElementById('no_dokumen');
        const noBuktiPembayaranElement = document.getElementById('no_bukti_pembayaran');
        const customerElement = document.getElementById('customer');
        const textareaElement = document.getElementById('keterangan');

        // Ambil teks dari elemen no_bukti_pembayaran
        const noBuktiPembayaranText = noBuktiPembayaranElement.value.trim();

        // Ambil semua opsi yang dipilih dari elemen <select>
        const selectedNoDokumen = Array.from(noDokumenElement.selectedOptions).map(option => `TERIMA A/ INVOICE ${option.text}`);
        const selectedCustomer = Array.from(customerElement.selectedOptions).map(option => option.text);

        // Gabungkan nilai opsi yang dipilih ke dalam textarea
        const combinedText = [...selectedCustomer, ...selectedNoDokumen].join('; ');
        textareaElement.value = combinedText;
    }

</script>

<?= $this->endSection(); ?>