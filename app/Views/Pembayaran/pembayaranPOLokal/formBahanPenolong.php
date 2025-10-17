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
                <?php if (can('Pembayaran', 'Lokal BP', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bp/print/' . encrypt($detail['pembayaranDetail']['id']) ?? '') ?>">
                        Print
                    </a>
                <?php endif; ?>

                <?php if ($detail['pembayaranDetail']['status_posting'] == "0") : ?>
                    <?php if (can('Pembayaran', 'Lokal BP', 'd')) : ?>
                        <button onclick="remove('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Lokal BP', 'a')) : ?>
                        <button onclick="posting('<?= encrypt($detail['pembayaranDetail']['id']) ?>')" class="btn btn-success posting-spp float-right posting">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Pembayaran', 'Lokal BP', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-form">
                            Simpan
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
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Pembayaran</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="<?= !empty($detail) ? encrypt($detail['pembayaranDetail']['id']) : ""; ?>">

                <input type="hidden" name="tanda_terima_faktur_id" class="tanda_terima_faktur_id" value="">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required readonly <?= !empty($detail) ? ' value="' . $detail['pembayaranDetail']['payment_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> value="<?= !empty($detail) ? date('d/m/Y', strtotime($detail['pembayaranDetail']['payment_date'])) : '' ?>">
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
                                <?= !empty($detail) && $detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '' ?>
                                name="jenis_pembayaran"
                                id="jenis_pembayaran"
                                required>
                                <?php if (empty($detail['pembayaranDetail']['jenis_pembayaran'])): ?>
                                    <option value="" selected disabled>Pilih Jenis Pembayaran</option>
                                <?php endif; ?>
                                <option value="MERAH" <?= !empty($detail['pembayaranDetail']['jenis_pembayaran']) && $detail['pembayaranDetail']['jenis_pembayaran'] == 'MERAH' ? 'selected' : '' ?>>MERAH</option>
                                <option value="PUTIH" <?= !empty($detail['pembayaranDetail']['jenis_pembayaran']) && $detail['pembayaranDetail']['jenis_pembayaran'] == 'PUTIH' ? 'selected' : '' ?>>PUTIH</option>
                            </select>
                            <label for="jenis_pembayaran">Jenis Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""; ?> name="bank_id" id="bank_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($bankList as $b) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['bank_id'] == $b->id ? 'selected' : '') : '' ?> value="<?= $b->id ?>"><?= strtoupper($b->kode_bank) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kode Bank (Opsional)</label>
                        </div>
                    </div>
                   <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option 
                                        <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier['id'] ? 'selected' : '') : '' ?> 
                                        value="<?= strtoupper($supplier['name']) ?>">
                                        <?= strtoupper($supplier['name']) ?>
                                        <?= !empty($supplier['companies_name']) ? ' - ' . strtoupper($supplier['companies_name']) : '' ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> 
                                class="form-select divisi_id" 
                                id="divisi_id" 
                                name="divisi_id" 
                                aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id']; ?>">
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
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="tanda_terima_supplier[]" id="tanda_terima_supplier">
                                <option value=""></option>
                                <?php if (!empty($detail)) : ?>
                                    <option value="<?= $detail['tandaTerimaSupplier']['id'] ?>" selected>
                                        <?= $detail['tandaTerimaSupplier']['faktur_no'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tanda Terima Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value=""></option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "bank" ? 'selected' : '') : '' ?> value="Bank">BANK</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "cash" ? 'selected' : '') : '' ?> value="Cash">CASH</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <textarea class="form-control" name="supplier" id="supplier" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'readonly' : '') : '' ?> placeholder="supplier"><?= !empty($detail) ? $detail['pembayaranDetail']['supplier'] : ''  ?></textarea>
                            <label for="floatingInput" style="z-index: 1;">Pembayaran Ke</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <textarea class="form-control" name="keterangan" id="keterangan" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'readonly' : '') : '' ?> placeholder="Keterangan" style="height: 220%;"><?= !empty($detail) ? $detail['pembayaranDetail']['keterangan'] : ''  ?></textarea>
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
                                                    <th style="text-align: center;">Barang</th>
                                                    <th style="text-align: center;">Qty</th>
                                                    <th style="text-align: center;">Satuan</th>
                                                    <th style="text-align: center;">Total Tagihan</th>
                                                    <th style="text-align: center;">Total Di Bayar</th>
                                                    <th style="text-align: center;">Sisa Tagihan</th>
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
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listPembayaran = [];

    <?php if (!empty($detail)) : ?>

        var id = $('#tanda_terima_supplier').val();
        var supplierId = $('#supplier_id option:selected').val();

        $.ajax({
            url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id + '/' + supplierId,
            method: "GET",
            data: {
                status_pph: $('#status_pph').val(),
                id: $('#id').val()
            },
            dataType: "json",
            success: function(res) {
                listPembayaran = [];
                listPembayaran = res.list;
                drawPaidTable(res);
            }
        })
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
                akun_kas: {
                    required: true
                },
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
                akun_kas: {
                    required: "Akun kas wajib diisi"
                },
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

        // $('#status_pph').select2({
        //     placeholder: "Status PPH",
        //     theme: "bootstrap-5"
        // }).change(function() {
        //     listBarangDetail();
        // });

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
            $('#supplier').val($('#supplier_id option:selected').text());
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
                            let nominalPembayaran = destroyFormatRupiah($('.nominal_pembayaran').val());
                            let nominalPembayaranPajak = destroyFormatRupiah($('.nominal_pembayaran_pajak').val());

                            const formatList = (list) => {
                                if (!Array.isArray(list)) return []; // Pastikan `list` adalah array
                                return list.map(item => {
                                    for (const key in item) {
                                        if (typeof item[key] === 'string' && item[key].includes(',')) {
                                            item[key] = destroyFormatRupiah(item[key]);
                                        }
                                    }
                                    return item;
                                });
                            };

                            formData.set('nominal_pembayaran', nominalPembayaran);
                            formData.set('nominal_pembayaran_pajak', nominalPembayaranPajak);
                            formData.append("pembayaranList", JSON.stringify(listPembayaran));
                            $.ajax({

                                url: "<?= base_url("/pembayaran-po-lokal-bp/update"); ?>",
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
                                                window.location.href = `<?= base_url("pembayaran-po-lokal-bp"); ?>`;
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
                            let nominalPembayaran = destroyFormatRupiah($('.nominal_pembayaran').val());
                            let nominalPembayaranPajak = destroyFormatRupiah($('.nominal_pembayaran_pajak').val());

                            const formatList = (list) => {
                                if (!Array.isArray(list)) return []; // Pastikan `list` adalah array
                                return list.map(item => {
                                    for (const key in item) {
                                        if (typeof item[key] === 'string' && item[key].includes(',')) {
                                            item[key] = destroyFormatRupiah(item[key]);
                                        }
                                    }
                                    return item;
                                });
                            };


                            formData.set('nominal_pembayaran', nominalPembayaran);
                            formData.set('nominal_pembayaran_pajak', nominalPembayaranPajak);
                            formData.append("pembayaranList", JSON.stringify(listPembayaran));
                            $.ajax({
                                url: "<?= base_url("pembayaran-po-lokal-bp/create"); ?>",
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
                                                window.location.href = `<?= base_url("pembayaran-po-lokal-bp/id/"); ?>` + response.id;
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

    function listBarangDetail() {
    var id = $('#tanda_terima_supplier').val();
    var supplierId = $('#supplier_id option:selected').val();
    $.ajax({
        url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id + '/' + supplierId,
        method: "GET",
        data: {
            status_pph: $('#status_pph').val()
        },
        beforeSend: function() {
            setLoading();
        },
        complete: function() {
            stopLoading();
        },
        dataType: "json",
        success: function(res) {
            listPembayaran = res.list;
            const table = $('#dataTable');
            const tbody = table.find('tbody');
            tbody.empty();

            if (!res.data || res.data.length === 0) {
                tbody.html('<tr><td colspan="10" style="color: whitesmoke;">Tidak Ada Data</td></tr>');
                return;
            }

            let no = 1;
            let grandTotalTagihan = 0;
            let grandTotalBayar = 0;
            let grandTotalSisa = 0;
            let grandTotalPajak = 0;
            let grandTotalPotongan = 0;
            let grandTotalTambahan = 0;

            // Process each tanda terima
            $.each(res.data, function(idx, tt) {
                const detail = tt.detail;
                const items = tt.list;
                const paymentDetail = tt.paymentDetail || [];
                
                // Calculate totals for this tanda terima
                const totalPajak = Number(tt.tax_dipungut_negara.taxAmt) + 
                                  Number(tt.tax_dikembalikan_lagi.taxAmt) + 
                                  Number(tt.pph);
                
                const totalBayar = paymentDetail.reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
                const sisaTagihan = Number(detail.nominal_faktur) - totalBayar;

                // Add to grand totals
                grandTotalTagihan += Number(detail.nominal_faktur);
                grandTotalBayar += totalBayar;
                grandTotalSisa += sisaTagihan;
                grandTotalPajak += totalPajak;
                grandTotalPotongan += Number(detail.potongan || 0);
                grandTotalTambahan += Number(detail.tambahan || 0);

                // Header for tanda terima (optional - bisa dihide kalo mau rapi)
                const headerRow = $('<tr class="table-warning">');
                headerRow.append(`<td colspan="10" style="text-align: center; background-color: #fff3cd;">
                    <b>Tanda Terima: ${detail.faktur_no} | Total: ${greatFormatRupiah(detail.nominal_faktur)} | Bayar: ${greatFormatRupiah(totalBayar)} | Sisa: ${greatFormatRupiah(sisaTagihan)}</b>
                </td>`);
                tbody.append(headerRow);

                // Process each item in this tanda terima
                $.each(items, function(i, item) {
                    const dateSplit = item.lpb_date.split('-');
                    const tanggal = dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];
                    
                    const row = $('<tr>');
                    row.append($('<td>').text(no++));
                    row.append($('<td>').text(tanggal));
                    row.append($('<td>').text(item.faktur_no || detail.faktur_no));
                    row.append($('<td>').text(item.lpb_no));
                    row.append($('<td>').text(item.item_name));
                    row.append($('<td>').text(item.qty));
                    row.append($('<td>').text(item.unit));
                    row.append($('<td>').text(greatFormatRupiah(item.price))); // Total Tagihan per item
                    row.append($('<td>').text(greatFormatRupiah(0))); // Total Dibayar per item (default 0)
                    row.append($('<td>').text(greatFormatRupiah(item.price))); // Sisa Tagihan per item (default sama dengan tagihan)
                    
                    tbody.append(row);
                });

                // Separator between tanda terima
                tbody.append('<tr><td colspan="10" style="padding: 5px; background-color: #f8f9fa;"></td></tr>');
            });

            // GRAND TOTAL ROW
            const grandTotalRow = $('<tr class="table-primary font-weight-bold">');
            grandTotalRow.append($('<td colspan="7" style="text-align: right;">').html('<b>GRAND TOTAL</b>'));
            grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalTagihan)}</b>`));
            grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalBayar)}</b>`));
            grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalSisa)}</b>`));
            tbody.append(grandTotalRow);

            // DETAIL SUMMARY ROW (Pajak, Potongan, Tambahan)
            const summaryRow = $('<tr class="table-info">');
            summaryRow.append($('<td colspan="7" style="text-align: right;">').html(`
                <small>
                    Total Pajak: ${greatFormatRupiah(grandTotalPajak)}<br>
                    Total Potongan: ${greatFormatRupiah(grandTotalPotongan)}<br>
                    Total Tambahan: ${greatFormatRupiah(grandTotalTambahan)}
                </small>
            `));
            summaryRow.append($('<td colspan="3">').html(`
                <div class="input-group">
                    <span class="input-group-text">Nominal Bayar:</span>
                    <input type="text" 
                           class="form-control nominalPembayaranInput" 
                           placeholder="Ketik nominal..." 
                           onkeyup="this.value = greatFormatRupiah(this.value)"
                           style="text-align: right; font-weight: bold;">
                    <button class="btn btn-success" type="button" onclick="processPayment()">Bayar</button>
                </div>
            `));
            tbody.append(summaryRow);
        }
    });
}

// Function untuk draw paid table (jika sudah ada pembayaran)
function drawPaidTable(res) {
    const table = $('#dataTable');
    const tbody = table.find('tbody');
    tbody.empty();

    if (!res.data || res.data.length === 0) {
        tbody.html('<tr><td colspan="10" style="color: whitesmoke;">Tidak Ada Pembayaran</td></tr>');
        return;
    }

    let no = 1;
    let grandTotalTagihan = 0;
    let grandTotalBayar = 0;
    let grandTotalSisa = 0;

    // Process each tanda terima yang sudah ada pembayaran
    $.each(res.data, function(idx, tt) {
        const detail = tt.detail;
        const items = tt.list;
        const paymentDetail = tt.paymentDetail || [];
        
        const totalBayar = paymentDetail.reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
        const sisaTagihan = Number(detail.nominal_faktur) - totalBayar;

        grandTotalTagihan += Number(detail.nominal_faktur);
        grandTotalBayar += totalBayar;
        grandTotalSisa += sisaTagihan;

        // Header tanda terima
        const headerRow = $('<tr class="table-warning">');
        headerRow.append(`<td colspan="10" style="text-align: center; background-color: #fff3cd;">
            <b>${detail.faktur_no} - ${detail.list_lpb}</b>
        </td>`);
        tbody.append(headerRow);

        // Items
        $.each(items, function(i, item) {
            const dateSplit = item.lpb_date.split('-');
            const tanggal = dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];
            
            const row = $('<tr>');
            row.append($('<td>').text(no++));
            row.append($('<td>').text(tanggal));
            row.append($('<td>').text(item.faktur_no || detail.faktur_no));
            row.append($('<td>').text(item.lpb_no));
            row.append($('<td>').text(item.item_name));
            row.append($('<td>').text(item.qty));
            row.append($('<td>').text(item.unit));
            row.append($('<td>').text(greatFormatRupiah(item.price)));
            row.append($('<td>').text(greatFormatRupiah(0))); // Dibayar per item
            row.append($('<td>').text(greatFormatRupiah(item.price))); // Sisa per item
            
            tbody.append(row);
        });

        // Summary per tanda terima
        const summaryRow = $('<tr class="table-secondary font-weight-bold">');
        summaryRow.append($('<td colspan="7" style="text-align: right;">').html(`<b>Total ${detail.faktur_no}</b>`));
        summaryRow.append($('<td>').html(`<b>${greatFormatRupiah(detail.nominal_faktur)}</b>`));
        summaryRow.append($('<td>').html(`<b>${greatFormatRupiah(totalBayar)}</b>`));
        summaryRow.append($('<td>').html(`<b>${greatFormatRupiah(sisaTagihan)}</b>`));
        tbody.append(summaryRow);

        tbody.append('<tr><td colspan="10" style="padding: 2px;"></td></tr>');
    });

    // Grand Total
    const grandTotalRow = $('<tr class="table-primary font-weight-bold">');
    grandTotalRow.append($('<td colspan="7" style="text-align: right;">').html('<b>GRAND TOTAL</b>'));
    grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalTagihan)}</b>`));
    grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalBayar)}</b>`));
    grandTotalRow.append($('<td>').html(`<b>${greatFormatRupiah(grandTotalSisa)}</b>`));
    tbody.append(grandTotalRow);
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

    function getListTandaTerimaSupplier() {
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
                beforeSend: function() {
                    setLoading();
                },
                complete: function() {
                    stopLoading();
                },
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
        var keterangan = "";
        var supplierName = $('#supplier_id option:selected').text();
        var noTandaTerima = $('#tanda_terima_supplier option:selected').text();
        var poNoText = "";
        $.each(data, function(i, v) {
            poNoText += `${v.item_name} Sebanyak ${v.qty} ${v.unit}, `;
        });

        keterangan = "Pembayaran " + supplierName + "; No TTS : " + noTandaTerima + "; " + poNoText;
        $('#keterangan').val(keterangan);
    }

    function generatePaymentNumber() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("type", "Bahan Penolong");
        formData.append("payment_date", $("#payment_date").val());
        formData.append("bankId", $("#bank_id option:selected").val());
        formData.append("divisiId", $("#divisi_id option:selected").text());
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