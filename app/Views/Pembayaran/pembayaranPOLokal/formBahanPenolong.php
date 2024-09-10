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
                            Update
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (can('Pembayaran', 'Lokal BP', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bp/print/' . encrypt($detail['pembayaranDetail']['id']) ?? '') ?>">
                        <i class="fa-solid fa-print"></i> Print
                    </a>
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
                                    <input autocomplete="one-time-code" type="text" class="form-control no_bukti_pembayaran" id="no_bukti_pembayaran" name="no_bukti_pembayaran" placeholder="No. Pembayaran" required <?= !empty($detail) ? 'disabled value="' . $detail['pembayaranDetail']['payment_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. Pembayaran</label>
                                </div>
                                <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px; <?= !empty($detail) ? 'display:none;' : '' ?>" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
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
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier['id'] ? 'selected' : '') : '' ?> value="<?= $supplier['id'] ?>"><?= strtoupper($supplier['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> name="tanda_terima_supplier" id="tanda_terima_supplier">
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
                </div>
                <!-- <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" onkeyup="this.value = formatRupiah(this.value);" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" readonly <?= !empty($detail) ? 'disabled value="' . " " . number_format($detail['pembayaranDetail']['amount'], 2, ',', '.')  . '"' : '' ?>>
                            <label for="floatingInput">Nominal Pembayaran</label>
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <!-- <div class="col-md-4" style="display: none;">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker jatuh_tempo" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal Jatuh Tempo" readonly >
                            <label for="floatingInput">Tanggal Jatuh Tempo</label>
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> class="form-select status_pph" name="status_pph" id="status_pph">
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['status_pph'] == "1" ? 'selected' : '') : '' ?> value="1">PPH 2.5 %</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['status_pph'] == "0" ? 'selected' : '') : 'selected' ?> value="0">TIDAK ADA</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Status PPH</label>
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
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0" style="border-color: #f7f6f5;">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th style="text-align: center;">No</th>
                                                    <th style="text-align: center;">Tanggal LPB</th>
                                                    <th style="text-align: center;">No. LPB</th>
                                                    <th style="text-align: center;">Barang</th>
                                                    <th style="text-align: center;">Qty</th>
                                                    <th style="text-align: center;">Satuan</th>
                                                    <th style="text-align: center;">Total</th>
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
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable-panjar" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th onclick="changeSort(' no_panjar')">No. Panjar</th>
                                            <th onclick="changeSort('payment_date')">Payment Date</th>
                                            <th onclick="changeSort('payment_amount')">Total Panjar</th>
                                            <th>Bayar Panjar </th>
                                            <th onclick="changeSort('payment_amt_left')">Sisa Panjar</th>

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

                <!-- <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">Item Tanda Terima Supplier</label>
                        </div>
                    </div>
                </div> -->
            </form>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    var listPanjar = [];

    <?php if (!empty($detail)) : ?>

        var id = $('#tanda_terima_supplier').val();
        $.ajax({

            url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id,
            method: "GET",
            data: {
                status_pph: $('#status_pph').val(),
                id: $('#id').val()
            },
            dataType: "json",
            success: function(res) {


                drawPaidTable(res);
                drawPaidPanjarTable(res);
                listPanjar = [];
                listPanjar = res.panjar_list;
                console.log(res);
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
                jatuh_tempo: {
                    required: "Tanggal jatuh tempo wajib diisi"
                },
                payment_method: {
                    required: "Metode pembayaran wajib diisi"
                },
                akun_kas: {
                    required: "Akun kas wajib diisi"
                },
                divisi_id: {
                    required: "Departemen wajib diisi"
                },
                status_pph: {
                    required: "Pilih status pph"
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

        $('#akun_kas').select2({
            placeholder: "Akun Debit",
            theme: "bootstrap-5"
        });

        $('#payment_method').select2({
            placeholder: "Metode Pembayaran",
            theme: "bootstrap-5"
        });

        $('#status_pph').select2({
            placeholder: "Status PPH",
            theme: "bootstrap-5"
        }).change(function() {
            listBarangDetail();
        });

        $('#akun_selisih').select2({
            placeholder: "Akun Selisih (Opsional)",
            theme: "bootstrap-5"
        });

        $('#divisi_id').select2({
            placeholder: "Pilih Departemen",
            theme: "bootstrap-5"
        }).change(function() {
            getListTandaTerimaSupplier();
        });

        $('#tanda_terima_supplier').select2({
            placeholder: "Pilih Tanda Terima Supplier",
            theme: "bootstrap-5"
        }).change(function() {
            listBarangDetail();
        });

        $('#supplier_id').select2({
            placeholder: "Pilih Supplier",
            theme: "bootstrap-5"
        }).change(function(e) {
            getListTandaTerimaSupplier();
        });

        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);

            // APPEND BAYAR PANJAR TO listPanjar

            $.each(listPanjar, function(i, v) {
                var element = $('input[data-id="' + v.id + '"].bayar_panjar');
                var input_user = (element.val());
                listPanjar[i].bayar_panjar = input_user;
            });

            if (id) {
                // UPDATE

                $.each(listPanjar, function(i, v) {
                    console.log(v.panjar_id);
                    var element = $('input[data-id="' + v.panjar_id + '"].bayar_panjar');
                    var input_user = (element.val());
                    console.log(input_user);
                    listPanjar[i].bayar_panjar = input_user;
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
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let formData = new FormData(document.querySelector(".create-form"));
                            formData.append("panjarList", JSON.stringify(listPanjar));
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
                            const data = new FormData(document.querySelector(".create-form"));
                            data.append("panjarList", JSON.stringify(listPanjar));
                            $.ajax({
                                url: "<?= base_url("pembayaran-po-lokal-bp/create"); ?>",
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
        $.ajax({
            url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id,
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
                const table = $('#dataTable');

                var detail = res.detail;
                var dateSplit = detail.jatuh_tempo.split('-');
                var subTotal = Number(detail.nominal_faktur) + Number(res.tax_dipungut_negara.taxAmt) + Number(res.pph) - Number(detail.total_amount);

                $('.nominal_pembayaran').val(formatRupiah(subTotal));
                $('.jatuh_tempo').val(dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0]);
                $('.tanda_terima_faktur_id').val(detail.id);


                // list append
                table.find('tbody').empty();
                var no = 1;
                $.each(res.list, function(i, v) {
                    var dateSplit = v.lpb_date.split('-');
                    var newRow = $('<tr>');
                    newRow.append($('<td>').text(no++));
                    newRow.append($('<td>').text(dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0]));
                    newRow.append($('<td>').text(v.lpb_no));
                    newRow.append($('<td>').text(v.item_name));
                    newRow.append($('<td>').text(v.qty));
                    newRow.append($('<td>').text(v.unit));
                    newRow.append($('<td>').text(formatRupiah(v.price)));
                    table.find('tbody').append(newRow);
                });
                var newRow1 = $('<tr>');
                newRow1.append($('<td style="text-align:right;" colspan="6">').text('Tambahan'));
                newRow1.append($('<td>').text(formatRupiah(detail.tambahan)));
                table.find('tbody').append(newRow1);

                var newRow2 = $('<tr>');
                newRow2.append($('<td style="text-align:right;" colspan="6">').text('Potongan'));
                newRow2.append($('<td>').text(formatRupiah(detail.potongan)));
                table.find('tbody').append(newRow2);

                var newRow3 = $('<tr>');
                newRow3.append($('<td style="text-align:right;" colspan="6">').text('Setelah Tambahan dan Potongan'));
                newRow3.append($('<td>').text(formatRupiah(detail.nominal_faktur)));
                table.find('tbody').append(newRow3);

                // var newRow4 = $('<tr>');
                // newRow4.append($('<td style="text-align:right;" colspan="6">').text('Pajak Dipungut Negara (' + res.tax_dipungut_negara.taxType + ')'));
                // newRow4.append($('<td>').text(formatRupiah(res.tax_dipungut_negara.taxAmt)));
                // table.find('tbody').append(newRow4);

                // var newRow5 = $('<tr>');
                // newRow5.append($('<td style="text-align:right;" colspan="6">').text('Pajak Dikembalikan Lagi (' + res.tax_dikembalikan_lagi.taxType + ')'));
                // newRow5.append($('<td>').text(formatRupiah(res.tax_dikembalikan_lagi.taxAmt)));
                // table.find('tbody').append(newRow5);

                var newRow6 = $('<tr>');
                newRow6.append($('<td style="text-align:right;" colspan="6">').text('Pajak Penghasilan (2.5 %) (+)'));
                newRow6.append($('<td>').text(formatRupiah(res.pph.toFixed(2))));
                table.find('tbody').append(newRow6);



                var newRow8 = $('<tr>');
                newRow8.append($('<td style="text-align:right;" colspan="6"><b>Potongan Panjar</b></td>'));
                newRow8.append($('<td style="text-align:center;"><b>' +
                    '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value="" name = "total_pembayaran_panjar" readonly>' +
                    '</b></td>'));
                table.find('tbody').append(newRow8);

                var newRow7 = $('<tr>');
                newRow7.append($('<td style="text-align:right;" colspan="6">').text('Sub Total'));
                newRow7.append($('<td class="subtotal">').text(formatRupiah(subTotal)));
                table.find('tbody').append(newRow7);



                $(document).on("input", ".bayar_panjar", function() {
                    var totalBayarPanjar = updateTotalBayarPanjar();
                    var newSubtotal = subTotal - totalBayarPanjar;
                    subCountTotal = newSubtotal;

                    $('input.nominal_pembayaran').attr('oninput', `limitInputBayar(this, ${subCountTotal})`);
                    $('.subtotal').text(formatRupiah(subCountTotal));

                });



                var newRow9 = $('<tr>');
                newRow9.append($('<td style="text-align:right;" colspan="6"><b>Input Pembayaran</b></td>'));
                newRow9.append($('<td>').html(
                    `
                        <input onchange="this.value = formatRupiah(this.value)" oninput="limitInputBayar(this,${subTotal})" autocomplete="one-time-code" data-id=""  class="form-control nominal_pembayaran" type="text" value="" name = "nominal_pembayaran" style="height:40px">
                    `
                ));
                table.find('tbody').append(newRow9);

            }
        })
    }

    function drawPaidPanjarTable(res) {
        const tablePanjar = $('#dataTable-panjar');
        tablePanjar.find('tbody').empty();
        tablePanjar.find('tfoot').empty();
        var panjar = res.panjar_list;

        let no = 1;
        $("#no_panjar").empty();
        tablePanjar.find('tbody').empty();




        if (panjar.length > 0) {
            $.each(panjar, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td style="width: 10px;">').text(no++));
                newRow.append($('<td>').text(v.no_panjar));
                newRow.append($('<td>').text((v.payment_date)));
                newRow.append($('<td>').text((formatRupiah(v.total_panjar))));
                sisa_panjar = Number(v.total_panjar) - Number(v.total_pembayaran.total_bayar_panjar);
                newRow.append($('<td>').html(
                    `
                            <input  onchange="this.value = formatRupiah(this.value)" class="form-control bayar_panjar" oninput="limitInputBayar(this, ${Number(v.bayar_panjar) + Number(sisa_panjar) })" autocomplete="one-time-code" data-id="${v.panjar_id}" type="text" value="${formatRupiah(v.bayar_panjar) }" name = "bayar_panjar" style="height:40px">
                                `
                ));
                newRow.append($('<td>').text((formatRupiah(sisa_panjar))));

                tablePanjar.find('tbody').append(newRow);
            })
        } else {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="8" style="text-align:center;">Tidak Ada Panjar</td>'));
            tablePanjar.find('tbody').append(newRow);
        }


    }

    function drawPaidTable(res) {
        const table = $('#dataTable');
        var detail = res.detail;
        var paymentDetail = res.paymentDetail;
        var panjar = res.panjar_list;
        var dateSplit = detail.jatuh_tempo.split('-');
        var form_total_bayar_panjar = 0;
        $.each(panjar, function(i, v) {
            form_total_bayar_panjar += Number(v.bayar_panjar);
        });
        var subTotal = Number(detail.nominal_faktur) + Number(res.tax_dipungut_negara.taxAmt) + Number(res.pph) - form_total_bayar_panjar;


        $('.nominal_pembayaran').val(formatRupiah(subTotal));
        $('.jatuh_tempo').val(dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0]);
        $('.tanda_terima_faktur_id').val(detail.id);

        table.find('tbody').empty();
        var no = 1;
        $.each(res.list, function(i, v) {
            var dateSplit = v.lpb_date.split('-');
            var newRow = $('<tr>');
            newRow.append($('<td>').text(no++));
            newRow.append($('<td>').text(dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0]));
            newRow.append($('<td>').text(v.lpb_no));
            newRow.append($('<td>').text(v.item_name));
            newRow.append($('<td>').text(v.qty));
            newRow.append($('<td>').text(v.unit));
            newRow.append($('<td>').text(formatRupiah(v.price)));
            table.find('tbody').append(newRow);
        });
        var newRow1 = $('<tr>');
        newRow1.append($('<td style="text-align:right;" colspan="6">').text('Tambahan'));
        newRow1.append($('<td>').text(formatRupiah(detail.tambahan)));
        table.find('tbody').append(newRow1);

        var newRow2 = $('<tr>');
        newRow2.append($('<td style="text-align:right;" colspan="6">').text('Potongan'));
        newRow2.append($('<td>').text(formatRupiah(detail.potongan)));
        table.find('tbody').append(newRow2);

        var newRow3 = $('<tr>');
        newRow3.append($('<td style="text-align:right;" colspan="6">').text('Setelah Tambahan dan Potongan'));
        newRow3.append($('<td>').text(formatRupiah(detail.nominal_faktur)));
        table.find('tbody').append(newRow3);
        var newRow6 = $('<tr>');
        newRow6.append($('<td style="text-align:right;" colspan="6">').text('Pajak Penghasilan (2.5 %) (+)'));
        newRow6.append($('<td>').text(formatRupiah(res.pph.toFixed(2))));
        table.find('tbody').append(newRow6);



        var newRow8 = $('<tr>');
        newRow8.append($('<td style="text-align:right;" colspan="6"><b>Potongan Panjar</b></td>'));
        newRow8.append($('<td style="text-align:center;"><b>' +
            '<input autocomplete="one-time-code" data-id=""  class="form-control total-bayar-panjar trigger-input" type="text" value="' + formatRupiah(form_total_bayar_panjar) + '" name = "total_pembayaran_panjar" readonly>' +
            '</b></td>'));
        table.find('tbody').append(newRow8);

        var newRow7 = $('<tr>');
        newRow7.append($('<td style="text-align:right;" colspan="6">').text('Sub Total'));
        newRow7.append($('<td class="subtotal">').text(formatRupiah(subTotal)));
        table.find('tbody').append(newRow7);



        $(document).on("input", ".bayar_panjar", function() {
            var totalBayarPanjar = updateTotalBayarPanjar();
            var newSubtotal = subTotal - totalBayarPanjar;
            subCountTotal = newSubtotal;

            $('input.nominal_pembayaran').attr('oninput', `limitInputBayar(this, ${subCountTotal})`);
            $('.subtotal').text(formatRupiah(subCountTotal));

        });

        var newRow9 = $('<tr>');
        newRow9.append($('<td style="text-align:right;" colspan="6"><b>Sisa Pembayaran</b></td>'));
        newRow9.append($(`<td style="text-align:center;"><b>${formatRupiah(detail.sisa)} </b></td>`));

        table.find('tbody').append(newRow9);


        var newRow10 = $('<tr>');
        newRow10.append($('<td style="text-align:right;" colspan="6"><b>Input Pembayaran</b></td>'));
        newRow10.append($('<td>').html(
            `
                        <input  <?= !empty($detail) ? ($detail['pembayaranDetail']['status_posting'] == 1 ? 'disabled' : '') : ""  ?> onchange="this.value = formatRupiah(this.value)" oninput="limitInputBayar(this,${Number(paymentDetail.amount) + Number(detail.sisa)})" autocomplete="one-time-code" data-id=""  class="form-control nominal_pembayaran" type="text" value="${formatRupiah(paymentDetail.amount) }" name = "nominal_pembayaran" style="height:40px">
                    `
        ));
        table.find('tbody').append(newRow10);
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
        var divisi_id = $('#divisi_id').val();

        if (supplier_id != "" && divisi_id != "") {
            var table = $('#dataTable');
            table.find('tbody').empty();
            $("#tanda_terima_supplier").empty();
            $.ajax({
                url: '<?= base_url('pembayaran-po-lokal-bp/get-rekap-faktur/') ?>' + supplier_id + '/' + divisi_id,
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("type", "Bahan Penolong");
        formData.append("payment_date", $("#payment_date").val());
        if (value) {
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

    function formatRupiah(angka) {
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
                        <input  class="form-control bayar_panjar" onchange="this.value = formatRupiah(this.value)" oninput="limitInputBayar(this, ${v.sisa_panjar_number})" autocomplete="one-time-code" data-id="${v.id}" type="text" value="" name = "bayar_panjar" style="height:40px">
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
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            input.value = '0';
        } else {
            input.value = numericValue;
        }
        if (numericValue > maxAmount) {
            input.value = maxAmount;
        }
    }

    function updateTotalBayarPanjar() {
        var sum = 0;
        $(".bayar_panjar").each(function() {
            sum += convertRupiahToNumber($(this).val());
        });
        $(".total-bayar-panjar").val(formatRupiah(sum));
        return sum;
    }

    $(document).on("input", ".bayar_panjar", function() {
        var totalBayarPanjar = updateTotalBayarPanjar();
        updateSubTotal(totalBayarPanjar);

    });

    function updateSubTotal(newSubtotal) {
        var countSubTotal = newSubtotal;
        return countSubTotal;
    }
</script>

<?= $this->endSection(); ?>