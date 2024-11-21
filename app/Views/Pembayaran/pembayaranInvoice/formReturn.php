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
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select customer_id " name="customer_id" id="customer_id" onchange="dropdownInvoice()">
                                <option value=""></option>
                                <?php foreach ($customers as $c): ?>
                                    <option <?= !empty($detail) ? ($detail['customer_id'] == $c['id'] ? 'selected' : '') : '' ?> value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select multiple <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select  no_dokumen" name="no_dokumen[]" id="no_dokumen">
                                <option value=""></option>
                                <?php if (isset($dokumenList)): ?>
                                    <?php foreach ($dokumenList as $d): ?>
                                        <option value="<?= $d['id'] ?>" selected><?= $d['no_return'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">No Dokumen</label>
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
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_kas_lain" id="akun_kas_lain">
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
                            <select <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select" name="akun_selisih_lain" id="akun_selisih_lain">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= (!empty($detail)) ?  (($detail['akun_selisih']) == $subs->id ? "selected" : "") : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit</label>
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
                            <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="nama_tagihan_lain_lain" id="nama_tagihan_lain_lain" autocomplete="one-time-code" value="" type="text" class="form-control" placeholder="Deskripsi Tagihan">
                            <label for="floatingInput">Nama Tagihan / Invoice</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> onchange="this.value = formatRupiah2(this.value)" name="total_tagihan_lain_lain" id="total_tagihan_lain_lain" autocomplete="one-time-code" value="" type="text" class="form-control" placeholder="Pembayaran Oleh">
                                <label for="floatingInput">Total Tagihan</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button class="btn btn-success btn-add-barang" data-toggle="modal" type="button" onclick="insertBarangLain()">
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
                                    <th style="text-align: center;">Kode Barang</th>
                                    <th style="text-align: center;">Nama Barang</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align: center;">Harga Satuan</th>
                                    <th style="text-align: center;">Sub Total</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="body-detail-table" id="body-detail-table" style="cursor: pointer;">

                            </tbody>
                            <tfoot>
                                <tr style="color: whitesmoke;">
                                    <td colspan="9" style="text-align: center;color:black;">Tidak ada data</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listData = [];
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

        $('#customer_id').select2({
            placeholder: "Pilih Customer",
            theme: "bootstrap-5",
            allowClear: true
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
            getBarangSalesReturn();
        })


        $('#payment_methods').select2({
            placeholder: "Pilih Metode Pembayaran",
            theme: "bootstrap-5",
            allowClear: true
        });

        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            var is_form_valid = true;
            var akun_kas_lain = $('#akun_kas_lain').val();
            var akun_selisih_lain = $('#akun_selisih_lain').val();

            for (let i = 0; i < listData.length; i++) {
                if (listData[i].kode_barang == "LAIN-LAIN") {
                    if (akun_kas_lain == "" || akun_selisih_lain == "" || akun_kas_lain == null || akun_selisih_lain == null) {
                        is_form_valid = false;
                        break;
                    }
                }
            }

            console.log(is_form_valid, akun_kas_lain, akun_selisih_lain);

            if (!is_form_valid) {
                Swal.fire({
                    icon: 'error',
                    title: "Akun Debit dan Akun Kredit Untuk Invoice Lain Wajib Diisi",
                    confirmButtonColor: '#4e73df',
                })
            } else {
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
                                formData.append('list_barang', JSON.stringify(listData));
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
                                let formData = new FormData(document.querySelector(".create-form"));
                                formData.append('total_amount_invoice', $(".total_amount_invoice").text());
                                formData.append('list_barang', JSON.stringify(listData));
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

    function formatRupiah2(angka) {
        if (angka === null || angka === undefined) {
            angka = 0;
        }

        angka = angka.toString().replace(/[^\d,]/g, '');
        let parts = angka.split(',');
        let ribuan = parts[0] || '0';
        let desimal = (parts[1] || '').padEnd(2, '0').substring(0, 2);

        let reverse = ribuan.split('').reverse().join('');
        let ribuanFormatted = reverse.match(/\d{1,3}/g).join('.').split('').reverse().join('');

        return ribuanFormatted + (desimal ? ',' + desimal : '');
    }

    function convertRupiahToNumber(rupiah) {
        if (!rupiah || typeof rupiah !== 'string') {
            return 0;
        }

        rupiah = rupiah.replace(/\./g, '').replace(',', '.');
        return parseFloat(rupiah) || 0;
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

    function insertBarangLain() {
        var nama_tagihan_lain_lain = $('#nama_tagihan_lain_lain').val();
        var total_tagihan_lain_lain = convertRupiahToNumber($('#total_tagihan_lain_lain').val());

        if (nama_tagihan_lain_lain == '' || total_tagihan_lain_lain == '') {
            Swal.fire({
                icon: 'error',
                title: 'Nama Tagihan dan Total Tagihan Wajib Diisi',
                confirmButtonColor: '#4e73df',
            });
        } else {
            listData.push({
                id: getID(),
                sales_order_return_detail_id: null,
                sales_order_return_id: null,
                qty_return: 1,
                harga_barang_return: (parseFloat(total_tagihan_lain_lain) || 0) * -1,
                amount_return: (parseFloat(total_tagihan_lain_lain) || 0) * -1,
                kode_barang: "LAIN-LAIN",
                barang_name: nama_tagihan_lain_lain
            });

            drawTable(listData);
            $('#nama_tagihan_lain_lain').val(null);
            $('#total_tagihan_lain_lain').val(null);
        }

    }

    function dropdownInvoice() {
        let customerId = $('#customer_id option:selected').val();
        $.ajax({
            url: `<?= base_url('pembayaran-invoice/dropdown-invoice-return'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                customer_id: $("#customer_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".no_dokumen").empty()
                $(".no_dokumen").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".no_dokumen").append(`<option value="${item.id}">${item.no_return}</option>`)
                })
                $(".no_dokumen").change();
            }
        });
    }

    function getBarangSalesReturn() {
        let arr = $('.no_dokumen').val();
        $.ajax({
            url: "<?= base_url("pembayaran-invoice/get-barang-sales-return"); ?>",
            method: "GET",
            dataSrc: "data",
            data: {
                id: JSON.stringify(arr),
                pembayaran_invoice_id: "<?= !empty($detail) ? encrypt($detail['id']) : '' ?>"
            },
            dataType: "json",
            success: function(res) {
                listData = [];
                listData = res.data;
                totalPembayaran = res.totalPembayaran;
                drawTable(listData)
            }
        })
    }

    function drawTable(listData) {

        const table = $('#dataTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (listData.length == 0) {
            table.find('tfoot').empty();
            var newRow = $('<tr style="color: whitesmoke;">');
            newRow.append($('<td colspan="9" style="text-align: center;color:black;">Tidak ada data</td>'));
            table.find('tfoot').append(newRow);
        } else {
            var total_amount = 0;
            var total_invoice = 0;
            var limit_bayar = 0;

            $.each(listData, function(index, item) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="text-align:center; display:none;" >').text(item.sales_order_return_detail_id));
                newRow.append($('<td style="text-align:center; display:none;" >').text(item.sales_order_return_id));
                newRow.append($('<td style="text-align:center;" >').text(item.kode_barang));
                newRow.append($('<td style="text-align:center;">').text(item.barang_name));
                newRow.append($('<td style="text-align:center;">').text(item.qty_return));
                newRow.append($('<td style="text-align:center;">').text(formatRupiah2(item.harga_barang_return * -1)));
                newRow.append($('<td style="text-align:center;">').text(formatRupiah2(item.amount_return * -1)));

                if (item.sales_order_return_detail_id == null) {
                    newRow.append(
                        $('<td style="text-align:center;">').html(`
                            <button type="button" class="btn btn-danger" onclick="deleteBarang('${item.id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        `)
                    );
                } else {
                    newRow.append($('<td style="text-align:center;">').text(''));
                }

                table.find('tbody').append(newRow);
                total_amount += parseFloat(item.amount_return);
            });
            total_invoice = totalPembayaran;
            var newRow0 = $('<tr style="color:whitesmoke;">');
            newRow0.append($('<td colspan="4" style="text-align: right;">').text("Total Pembayaran"));
            newRow0.append($('<td style="text-align:center;">').text(formatRupiah2(total_amount)));
            table.find('tbody').append(newRow0);

            var newRow1 = $('<tr style="color:whitesmoke;" class="total-dibayar-row">');
            newRow1.append($('<td colspan="4" style="text-align: right;">').text("Total Sudah Dibayar"));
            newRow1.append($('<td class="total_dibayar" style="text-align:center;">').text(formatRupiah2(total_invoice)));
            table.find('tbody').append(newRow1);

            var newRow2 = $('<tr style="color:whitesmoke;">');
            newRow2.append($('<td colspan="4" style="text-align: right;">').text("Sisa Pembayaran"));
            newRow2.append($('<td class="total_amount_invoice" style="text-align:center;">').text(formatRupiah2(total_amount - total_invoice)));
            table.find('tbody').append(newRow2);

            limit_bayar = parseFloat(total_amount) - parseFloat(total_invoice);

            var newRow3 = $('<tr style="color:whitesmoke;">');
            newRow3.append($('<td colspan="4" style="text-align: right;">').text("Potongan"));
            newRow3.append($('<td style="text-align:center;"><b>' +
                `<input autocomplete="one-time-code" data-id=""<?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> onchange="this.value=formatRupiah2(this.value);validateKeyup();"  class="form-control potongan trigger-input" type="text" value="<?= !empty($detail) ? formatRupiah($detail['potongan'])  : '' ?>" name="potongan" onchange="limitInputBayar(this, ${limit_bayar})">` +
                '</b></td>'));
            table.find('tbody').append(newRow3);

            var newRow4 = $('<tr style="color:whitesmoke;">');
            newRow4.append($('<td colspan="4" style="text-align: right;">').text("Anda Membayar Sebesar"));
            newRow4.append($('<td style="text-align:center;"><b>' +
                `<input autocomplete="one-time-code" data-id="" <?= !empty($detail) ? ($detail['status_posting'] == 1 ? 'disabled' : '') : ""  ?> onchange="this.value=formatRupiah2(this.value);validateKeyup();" class="form-control total-bayar trigger-input" id="total_bayar" type="text" value="<?= !empty($detail) ? formatRupiah($detail['total_bayar'])  : '' ?>" name="total_bayar"> ` +
                '</b></td>'));
            table.find('tbody').append(newRow4);

            $(document).on("input", ".total-bayar, .potongan", function() {
                let totalAmount = parseFloat($('.total_amount_invoice').text().replace(/\./g, '').replace(',', '.'));
                var potongan = $('.potongan').val() ? convertRupiahToNumber($('.potongan').val()) : 0;
                limit_bayar = totalAmount - parseFloat(potongan);

                $('input.total-bayar').attr('onchange', `limitInputBayar(this, ${limit_bayar})`);

                if ($('.potongan').val() != "" || $('.potongan').val() != 0) {
                    $('.total-bayar').val(limit_bayar).change()
                }
            });
        }
    }

    function deleteBarang(id) {
        var indexToRemove = -1;
        for (let i = 0; i < listData.length; i++) {
            if (listData[i].id == id) {
                indexToRemove = i;
                break;
            }
        }

        if (indexToRemove !== -1) {
            listData.splice(indexToRemove, 1);
            drawTable(listData)
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
</script>

<?= $this->endSection(); ?>