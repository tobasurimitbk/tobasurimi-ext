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
                    <a class="nav-link active" href="#">Pembayaran Invoice Lokal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('pembayaran-invoice/create-ekspor') ?>">Pembayaran Invoice Ekspor</a>
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
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select customer" name="customer" id="customer">
                                <option value=""></option>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $cus): ?>
                                        <option <?= (!empty($detail)) ?  (($detail['customer_id']) == $cus['id'] ? "selected" : "") : '' ?> value="<?= encrypt($cus['id']); ?>"><?= $cus['name']; ?></option>
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
                            <textarea <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> autocomplete="one-time-code" class="form-control keterangan" id="keterangan" name="keterangan"><?= !empty($detail) ? $detail['keterangan'] : "" ?> </textarea>
                            <label for="floatingInput">Keterangan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_kas_lain" id="akun_kas_lain">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= (!empty($detail)) ?  (($detail['akun_kas_lain']) == $subs->id ? "selected" : "") : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit Lain (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_selisih_lain" id="akun_selisih_lain">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= (!empty($detail)) ?  (($detail['akun_selisih_lain']) == $subs->id ? "selected" : "") : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit Lain (Opsional)</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-light">
                            Jika ada Tagihan Diluar Invoice (Tagihan Lain-Lain), Silahkan Diinputkan Pada Form Dibawah
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="nama_tagihan" id="nama_tagihan" autocomplete="one-time-code" value="" type="text" class="form-control" placeholder="Deskripsi Tagihan">
                            <label for="floatingInput">Nama Tagihan / Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="total_tagihan" id="total_tagihan" autocomplete="one-time-code" value="" type="text" class="form-control" placeholder="Total Tagihan" onchange="this.value = formatRupiah2(this.value)">
                                <label for="floatingInput">Total Tagihan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-success btn-add-barang" onclick="insertBarangLain()" data-toggle="modal" type="button">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <th style="text-align: center;">Action</th>
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
        $('#akun_kas_lain').select2({
            placeholder: "Akun Debit Lain",
            theme: "bootstrap-5"
        });
        $('#akun_selisih_lain').select2({
            placeholder: "Akun Kredit Lain (Opsional)",
            theme: "bootstrap-5"
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

        // Trigger event change jika sudah ada nilai default
        const selectedCustomerId = "<?= !empty($detail) ? encrypt($detail['customer_id']) : ''; ?>";
        if (selectedCustomerId) {
            $('#customer').val(selectedCustomerId).trigger('change'); // Trigger change secara manual
        }


        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        });

        $('#no_dokumen').select2({
            placeholder: "Pilih Nomor Dokumen",
            theme: "bootstrap-5",
            allowClear: true
        }).change(function() {
            getDataSalesLokal();
        });


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
                            formData.append('list_barang', JSON.stringify(dataList));
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
                            formData.append('total_amount_invoice', $(".total_amount_invoice").text());
                            formData.append('list_barang', JSON.stringify(dataList));
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
        $.ajax({
            url: `<?= base_url("pembayaran-invoice/get-dokumen-invoice-lokal"); ?>/${customerId}`,
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
                    res.data.forEach((data) => {
                        // Periksa apakah data sudah ada di dataList sebelum menambahkannya
                        if (!dataList.some(item => item.sales_order_invoice_id === data.sales_order_invoice_id)) {
                            dataList.push({
                                id: getID(),
                                qty_invoice: data.qty_invoice,
                                harga_barang_invoice: data.harga_barang_invoice,
                                amount_invoice: data.amount_invoice,
                                kode_barang: data.kode_barang,
                                barang_name: data.barang_name,
                                sales_order_invoice_id: data.sales_order_invoice_id,
                                sales_order_invoice_detail_id: data.sales_order_invoice_detail_id,
                                no_faktur: data.no_faktur
                            });
                        }
                    });
                }

                const totalPembayaran = parseFloat(res.totalPembayaran) || 0;

                // Refresh tabel dengan data terbaru
                drawTable(dataList, totalPembayaran);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function drawTable(dataList, totalPembayaran) {
        const table = $('#dataTable');
        table.find('tbody').empty();

        let total_amount = 0;
        let total_invoice = 0;
        let limit_bayar = 0;
        let total_invoice_barang_lain = 0;
        let total_invoice_non_lain = 0;

        // Mengelompokkan data berdasarkan no_faktur
        let groupedData = {};
        $.each(dataList, function(index, item) {
            if (!groupedData[item.no_faktur]) {
                groupedData[item.no_faktur] = [];
            }
            groupedData[item.no_faktur].push(item);
        });

        // Loop untuk menambahkan baris ke tabel
        $.each(groupedData, function(noFaktur, group) {
            let newRow = $('<tr style="color:whitesmoke;">');
            newRow.append($('<td rowspan="' + group.length + '" style="text-align:center;">').text(noFaktur));

            $.each(group, function(index, item) {
                if (index === 0) {
                    newRow.append($('<td style="text-align:center;">').text(item.kode_barang));
                    newRow.append($('<td style="text-align:center;">').text(item.barang_name));
                    newRow.append($('<td style="text-align:center;">').text(item.qty_invoice));
                    newRow.append($('<td style="text-align:center;">').text(formatRupiah2(item.harga_barang_invoice)));
                    newRow.append($('<td style="text-align:center;">').text(formatRupiah2(item.amount_invoice)));

                    if (item.kode_barang == "LAIN-LAIN") {
                        total_invoice_barang_lain += parseFloat(item.amount_invoice) || 0;
                        newRow.append($('<td style="text-align:center;">').html(`
                            <button type="button" class="btn btn-danger" onclick="deleteBarang('${item.id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        `));
                    } else {
                        total_invoice_non_lain += parseFloat(item.amount_invoice) || 0;
                        newRow.append($('<td style="text-align:center;">').html(``));
                    }
                    table.find('tbody').append(newRow);
                } else {
                    let newRowItem = $('<tr style="color:whitesmoke;">');
                    newRowItem.append($('<td style="text-align:center;">').text(item.kode_barang));
                    newRowItem.append($('<td style="text-align:center;">').text(item.barang_name));
                    newRowItem.append($('<td style="text-align:center;">').text(item.qty_invoice));
                    newRowItem.append($('<td style="text-align:center;">').text(formatRupiah2(item.harga_barang_invoice)));
                    newRowItem.append($('<td style="text-align:center;">').text(formatRupiah2(item.amount_invoice)));
                    newRowItem.append($('<td style="text-align:center;">').html(``));
                    table.find('tbody').append(newRowItem);
                }
            });
        });

        // Total amount calculation
        total_amount = total_invoice_non_lain - total_invoice_barang_lain;

        // Menambahkan baris total pembayaran dan potongan
        addSummaryRows(table, total_amount, totalPembayaran, limit_bayar);
    }

    function insertBarangLain() {
        const namaTagihan = $('#nama_tagihan').val();
        const totalTagihan = $('#total_tagihan').val();

        if (namaTagihan === '' || totalTagihan === '') {
            Swal.fire({
                icon: 'error',
                title: "Nama Tagihan dan Total Tagihan Wajib Diisi",
                confirmButtonColor: '#4e73df',
            });
        } else {
            // Tambahkan item baru ke dataList
            dataList.push({
                id: getID(),
                no_faktur: "LAIN-LAIN", // Jika tidak ada `no_faktur`, gunakan default
                qty_invoice: 1,
                harga_barang_invoice: convertRupiahToNumber(totalTagihan),
                amount_invoice: convertRupiahToNumber(totalTagihan),
                kode_barang: "LAIN-LAIN",
                barang_name: namaTagihan,
                sales_order_invoice_id: null,
                sales_order_invoice_detail_id: null
            });

            // Refresh tabel dengan data terbaru
            drawTable(dataList);

            // Reset input
            $('#nama_tagihan').val(null);
            $('#total_tagihan').val(null);
        }
    }

    function addSummaryRows(table, total_amount, total_invoice, limit_bayar) {
        // Pastikan semua parameter memiliki nilai default 0 jika undefined, null, atau NaN
        total_amount = isNaN(total_amount) ? 0 : total_amount;
        total_invoice = isNaN(total_invoice) ? 0 : total_invoice;
        limit_bayar = isNaN(limit_bayar) ? 0 : limit_bayar;

        // Tambahkan baris untuk Total Pembayaran, Total Sudah Dibayar, dan Sisa Pembayaran
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Total Pembayaran</td>
                <td style="text-align:center;">${formatRupiah2(total_amount)}</td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Total Sudah Dibayar</td>
                <td class="total_dibayar" style="text-align:center;">${formatRupiah2(total_invoice)}</td>
            </tr>
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Sisa Pembayaran</td>
                <td class="total_amount_invoice" style="text-align:center;">${formatRupiah2(total_amount - total_invoice)}</td>
            </tr>
        `);

        // Tambahkan baris untuk input Total Bayar
        table.find('tbody').append(`
            <tr style="color:whitesmoke;">
                <td colspan="5" style="text-align: right;">Anda Membayar Sebesar</td>
                <td style="text-align:center;">
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
        table.find('input.total-bayar').on('input', function() {
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
            $(this).val(formatRupiah2(numberValue));
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

    function getID() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let randomString = '';

        for (let i = 0; i < 10; i++) {
            randomString += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return randomString;
    };


    <?php if (!empty($detail)) : ?>
        getDataSalesLokal();

    <?php endif; ?>
</script>

<?= $this->endSection(); ?>