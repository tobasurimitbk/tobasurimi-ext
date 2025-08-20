<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name"><?= !empty($detail) ? "Update Pembayaran Invoice Ekspor" : "Tambah Pembayaran Invoice Ekspor" ?></h1>
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
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create') ?>">Pembayaran Invoice Lokal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Pembayaran Invoice Ekspor</a>
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
                <input autocomplete="one-time-code" type="hidden" class="tipe_invoice" name="tipe_invoice" id="tipe_invoice" value="<?= !empty($detail) ? encrypt($detail['type_invoice']) : "EKSPOR"; ?>">

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
                                <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> value="<?= !empty($detail) ? date('d/m/Y', strtotime($detail['tanggal'])) : '' ?>">
                                <label for="floatingInput">Tanggal Pembayaran</label>
                            </div>
                            <div class="input-group-prepend group-prepend-password align-items-center">
                                <i style="cursor: pointer; z-index: 99; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select jenis_dokumen" name="jenis_dokumen" id="jenis_dokumen">
                                <option disabled selected value=""></option>
                                <option value="lokal">Lokal</option>
                                <option value="ekspor">Ekspor</option>
                                <option value="lain">Lain-lain</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Jenis Dokumen</label>
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select no_dokumen" name="no_dokumen" id="no_dokumen">
                                <option value=""></option>
                                <?php if (!empty($dokumenList)): ?>
                                    <?php foreach ($dokumenList as $l): ?>
                                        <option <?= (!empty($detail)) ?  (($detail['invoice_id']) == $l['sales_order_export_id'] ? "selected" : "") : '' ?> value="<?= encrypt($l['sales_order_export_id']); ?>"><?= $l['sales_order_export_no']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No Dokumen</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!-- <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($divisi)): ?>
                                    <?php foreach ($divisi as $d): ?>
                                        <option value="<?= $d['id']; ?>"><?= $d['divisi']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div> -->
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

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select payment_methods " name="payment_methods" id="payment_methods">
                                <option value=""></option>
                                <option value="BANK">BANK</option>
                                <option value="CASH">CASH</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Payment Methods</label>
                        </div>
                    </div>
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
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <textarea <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> autocomplete="one-time-code" class="form-control keterangan" id="keterangan" name="keterangan"><?= !empty($detail) ? $detail['keterangan'] : "" ?></textarea>
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
                                        <th style="text-align: center;">Kode Barang</th>
                                        <th style="text-align: center;">Nama Barang</th>
                                        <th style="text-align: center;">Spesifikasi Barang</th>
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


        $('#akun_selisih').select2({
            placeholder: "Akun Selisih (Opsional)",
            theme: "bootstrap-5"
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });
        $('#no_dokumen').select2({
            placeholder: "Pilih Nomor Dokumen",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            getValas();
            drawTable();
            getCustomer();
        })



        $('#payment_methods').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            allowClear: true
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
                            let formData = new FormData(document.querySelector(".create-form"));
                            formData.append('total_amount_invoice', $(".total_amount_invoice").text());
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
                            const data = new FormData(document.querySelector(".create-form"));
                            data.append('total_amount_invoice', $(".total_amount_invoice").text());
                            $.ajax({
                                url: "<?= base_url("pembayaran-invoice/save"); ?>",
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
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
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
            url: "<?= base_url("pembayaran-invoice/get-valas-sales-ekspor"); ?>",
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

    function drawTable() {
        $.ajax({
            url: "<?= base_url("pembayaran-invoice/get-barang-sales-ekspor"); ?>",
            method: "GET",
            dataSrc: "data",
            data: {
                id: $("#no_dokumen").val()
            },
            beforeSend: function(xhr) {
                // setLoading();
                // xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            complete: function() {
                // stopLoading();
            },
            dataType: "json",
            success: function(res) {
                const table = $('#dataTable');
                table.find('tbody').empty();
                var total_amount = 0;
                var total_invoice = 0;
                var limit_bayar = 0;

                // barang
                $.each(res.data, function(index, item) {
                    var newRow = $('<tr style="color:whitesmoke;">');
                    newRow.append($('<td style="text-align:center;" >').text(item.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(item.barang_name));
                    newRow.append($('<td style="text-align:center;">').text(item.specs));
                    newRow.append($('<td style="text-align:center;">').text(item.qty));
                    newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(item.harga_barang)));
                    newRow.append($('<td style="text-align:center;">').text(greatFormatRupiah(item.total_harga)));
                    table.find('tbody').append(newRow);
                    total_amount += parseFloat(item.total_harga);
                });

                // total pembayaran
                total_invoice = res.totalPembayaran;

                var newRow0 = $('<tr style="color:whitesmoke;">');
                newRow0.append($('<td colspan="5" style="text-align: right;">').text("Total Harga Barang"));
                newRow0.append($('<td style="text-align:center;">').text(greatFormatRupiah(total_amount)));
                table.find('tbody').append(newRow0);

                // mapping label + rule (+ berarti nambah biaya, - berarti ngurangin biaya)
                const biayaMap = {
                    commision: { label: "Commission", sign: "-" },
                    palet_fumigation: { label: "Palet Fumigation", sign: "-" },
                    palet_fumigation_price: { label: "Palet Fumigation Price", sign: "-" },
                    freight: { label: "Freight", sign: "-" },
                    additional: { label: "Additional", sign: "-" },
                    additional_2: { label: "Additional 2", sign: "-" },
                    rebate_price: { label: "Rebate Price", sign: "-" },
                    royalty_price: { label: "Royalty Price", sign: "-" },
                    estimated_freight_price: { label: "Estimated Freight Price", sign: "-" },
                    can_deduction_price: { label: "Can Deduction", sign: "+" } // special case
                };

                var tambahan = 0;
                const extra = res.salesOrderExportData;
                // looping semua key selain other_price
                for (const key in biayaMap) {
                    if (extra[key] && parseFloat(extra[key]) != 0) {
                        const val = parseFloat(extra[key]);
                        const row = $('<tr style="color:whitesmoke;">');
                        row.append($('<td colspan="5" style="text-align: right;">').text(biayaMap[key].label));

                        // tampilkan dengan tanda plus/minus biar jelas
                        let displayVal = (biayaMap[key].sign === "-" ? "- " : "+ ") + greatFormatRupiah(val);
                        row.append($('<td style="text-align:center;">').text(displayVal));
                        table.find('tbody').append(row);

                        // hitung total
                        if (biayaMap[key].sign === "+") {
                            tambahan += val;   // biaya menambah total
                        } else {
                            tambahan -= val;   // deduction mengurangi total
                        }
                    }
                }

                // handle khusus untuk other_price + other_type
                if (extra.others_price && parseFloat(extra.others_price) != 0) {
                    const val = parseFloat(extra.others_price);
                    const type = (extra.others_type || "PLUS").toUpperCase(); // default PLUS kalau kosong
                    const row = $('<tr style="color:whitesmoke;">');
                    row.append($('<td colspan="5" style="text-align: right;">').text("Other Price"));

                    let displayVal = (type === "MINUS" ? "- " : "+ ") + greatFormatRupiah(val);
                    row.append($('<td style="text-align:center;">').text(displayVal));
                    table.find('tbody').append(row);

                    if (type === "PLUS") {
                        tambahan += val;
                    } else {
                        tambahan -= val;
                    }
                }


                // ================= Tambahin salesOrderExportAdditionalData (dinamis) =================
                if (res.salesOrderExportAdditionalData && res.salesOrderExportAdditionalData.length > 0) {
                    $.each(res.salesOrderExportAdditionalData, function(i, item) {
                        var val = parseFloat(item.additional_detail_price);
                        if (item.additional_detail_type === "PLUS") {
                            total_amount += val;
                        } else {
                            total_amount -= val;
                        }

                        var row = $('<tr style="color:whitesmoke;">');
                        row.append($('<td colspan="5" style="text-align:right;">').text(item.additional_detail));
                        row.append($('<td style="text-align:center;">').text(greatFormatRupiah(val)));
                        table.find('tbody').append(row);
                    });
                }

                // total akhir
                var grandTotal = total_amount + tambahan;

                var newRowGrand = $('<tr style="color:yellow; font-weight:bold;">');
                newRowGrand.append($('<td colspan="5" style="text-align: right;">').text("Grand Total"));
                newRowGrand.append($('<td style="text-align:center;">').text(greatFormatRupiah(grandTotal)));
                table.find('tbody').append(newRowGrand);


                // total sudah dibayar
                var newRow1 = $('<tr style="color:whitesmoke;">');
                newRow1.append($('<td colspan="5" style="text-align: right;">').text("Total Sudah Dibayar"));
                newRow1.append($('<td class="total_dibayar" style="text-align:center;">').text(greatFormatRupiah(total_invoice)));
                table.find('tbody').append(newRow1);

                // sisa pembayaran
                var newRow2 = $('<tr style="color:whitesmoke;">');
                newRow2.append($('<td colspan="5" style="text-align: right;">').text("Sisa Pembayaran"));
                newRow2.append($('<td class="total_amount_invoice" style="text-align:center;">').text(greatFormatRupiah(grandTotal - total_invoice)));
                table.find('tbody').append(newRow2);

                limit_bayar = parseFloat(grandTotal) - parseFloat(total_invoice);

                // Potongan
                var potonganVal = `<?= !empty($detail) ? formatRupiah($detail['potongan']) : '' ?>`;
                var newRow3 = $('<tr style="color:whitesmoke;">');
                newRow3.append($('<td colspan="5" style="text-align: right;">').text("Potongan"));
                newRow3.append($('<td style="text-align:center;"><b>' +
                    `<input autocomplete="one-time-code" data-id="" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : "" ?>
                        onchange="this.value = greatFormatRupiah(this.value)"
                        class="form-control potongan trigger-input"
                        type="text"
                        value="${potonganVal}"
                        name="potongan"
                        oninput="limitInputBayar(this, ${limit_bayar})">` +
                    '</b></td>'));
                table.find('tbody').append(newRow3);

                // Total Bayar
                // kalau value dari BE kosong/null → fallback pakai limit_bayar
                var bayarVal = `<?= !empty($detail) ? formatRupiah($detail['total_bayar']) : '' ?>`;
                if (!bayarVal || bayarVal === "0") {
                    bayarVal = greatFormatRupiah(limit_bayar);
                }

                var newRow4 = $('<tr style="color:whitesmoke;">');
                newRow4.append($('<td colspan="5" style="text-align: right;">').text("Anda Membayar Sebesar"));
                newRow4.append($('<td style="text-align:center;"><b>' +
                    `<input autocomplete="one-time-code" data-id="" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : "" ?>
                        onchange="this.value = greatFormatRupiah(this.value)"
                        class="form-control total-bayar trigger-input"
                        type="text"
                        value="${bayarVal}"
                        name="total_bayar"> ` +
                    '</b></td>'));
                table.find('tbody').append(newRow4);


                $(document).on("input", ".total-bayar, .potongan", function() {
                    var potongan = $('.potongan').val() ? convertRupiahToNumber($('.potongan').val()) : 0;

                    // hitung ulang limit_bayar dengan potongan
                    var newLimit = parseFloat(grandTotal) - parseFloat(total_invoice) - parseFloat(potongan);

                    // update attribute limitInputBayar pakai nilai baru
                    $('input.total-bayar').attr('oninput', `limitInputBayar(this, ${newLimit})`);

                    // kalau ada potongan valid, auto update total bayar
                    if ($('.potongan').val() !== "" && potongan > 0) {
                        $('.total-bayar').val(greatFormatRupiah(newLimit)).change();
                    }
                });

            }
        })
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
    <?php if (!empty($detail)) : ?>
        drawTable();

    <?php endif; ?>
</script>

<?= $this->endSection(); ?>