<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah Pembayaran Lokal Bahan Penolong</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal-bp"); ?>">
                Batal
            </a>
            <?php if (!empty($detail)) : ?>
                <?php if (can('Pembayaran', 'Lokal BP', 'p')) : ?>
                    <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bp/print/' . $detail['pembayaranDetail']['id'] ?? '') ?>">
                        <i class="fa-solid fa-print"></i> Print
                    </a>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-form">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" value="" />
                <input type="hidden" name="tanda_terima_faktur_id" class="tanda_terima_faktur_id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'disabled value="' . $detail['pembayaranDetail']['payment_date'] . '"' : '' ?>>
                            <label for="floatingInput">Tanggal Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier->id ? 'selected' : '') : '' ?> value="<?= $supplier->id ?>"><?= strtoupper($supplier->name) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="tanda_terima_supplier" id="tanda_terima_supplier">
                                <option value=""></option>
                                <?php if (!empty($detail)) : ?>
                                    <option value="<?= $detail['tandaTerimaSupplier']['faktur_no'] ?>" selected>
                                        <?= $detail['tandaTerimaSupplier']['faktur_no'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tanda Terima Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" onkeyup="this.value = formatRupiah(this.value);" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" readonly <?= !empty($detail) ? 'disabled value="' . " " . number_format($detail['pembayaranDetail']['amount'], 2, ',', '.')  . '"' : '' ?>>
                            <label for="floatingInput">Nominal Pembayaran</label>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker jatuh_tempo" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal Jatuh Tempo" readonly <?= !empty($detail) ? 'value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['due_date']))  . '"' : '' ?>>
                            <label for="floatingInput">Tanggal Jatuh Tempo</label>
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? 'disabled' : '' ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value=""></option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Bank">Bank</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Cash">Cash</option>
                                <!-- <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Debit" ? 'selected' : '') : '' ?> value="Debit">Debit</option> -->
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($detail) ? 'disabled' : '' ?> name="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_kas" id="akun_kas">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_kas'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Debit</label>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="akun_selisih" id="akun_selisih">
                                <option disabled selected value=""></option>
                                <?php foreach ($subsAkuns as $subs) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['akun_selisih'] == $subs->id ? 'selected' : '') : '' ?> value="<?= $subs->id ?>"><?= strtoupper($subs->no_sub . " " . $subs->nama_sub) ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Kredit (Opsional)</label>
                        </div>
                    </div>
                </div>

                <div class="col-subtitle-modal mt-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label font-weight-bold modal-sub-title">Item List</label>
                        </div>
                    </div>
                </div>
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
                                    <?php if (!empty($detail)) : ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($detail['itemLpbList'] as $d) : ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= date('d/m/Y', strtotime($d['lpb_date'])) ?></td>
                                                <td><?= $d['lpb_no'] ?></td>
                                                <td><?= $d['item_name'] ?></td>
                                                <td><?= $d['qty'] ?></td>
                                                <td><?= $d['unit'] ?></td>
                                                <td><?= " " . number_format($d['price'], 2, ',', '.')  ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Tambahan
                                            </td>
                                            <td><?= " " . number_format($detail['tandaTerimaSupplier']['tambahan'], 2, ',', '.')  ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Potongan
                                            </td>
                                            <td><?= " " . number_format($detail['tandaTerimaSupplier']['potongan'], 2, ',', '.')  ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Setelah Tambahan dan Potongan
                                            </td>
                                            <td><?= " " . number_format($detail['tandaTerimaSupplier']['nominal_faktur'], 2, ',', '.')  ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Pajak Dipungut Negara (<?= $tax_dipungut_negara['taxType'] ?>)
                                            </td>
                                            <td><?= " " . number_format($tax_dipungut_negara['taxAmt'], 2, ',', '.')  ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Pajak Dikembalikan Lagi (<?= $tax_dikembalikan_lagi['taxType'] ?>)
                                            </td>
                                            <td><?= " " . number_format($tax_dikembalikan_lagi['taxAmt'], 2, ',', '.')  ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Sub Total
                                            </td>
                                            <td><?= " " . number_format(($detail['tandaTerimaSupplier']['nominal_faktur'] + $tax_dipungut_negara['taxAmt']), 2, ',', '.')  ?></td>
                                        </tr>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="7">
                                                Barang tidak ada
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#dataTable');

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
                }
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
                $(element).closest('.col-md-6').addClass('has-error');
                $(element).addClass('select-class');

            },
            unhighlight: function(element) {
                $(element).closest('.col-md-6').removeClass('has-error');
                $(element).removeClass('select-class');
            },
        });

        $("#payment_date").datepicker({
            todayHighlight: true,
            format: "dd/mm/yyyy",
            orientation: "bottom auto",
            autoclose: true
        });

        $('#akun_kas, #akun_selisih').select2({
            placeholder: "",
            theme: "bootstrap-5"
        });

        $('#tanda_terima_supplier').select2({
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function() {
            var id = $('#tanda_terima_supplier').val();
            $.ajax({
                url: '<?= base_url('pembayaran-po-lokal-bp/get-item-list/') ?>' + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    // detail append
                    var detail = res.detail;
                    var dateSplit = detail.jatuh_tempo.split('-');
                    var subTotal = Number(detail.nominal_faktur) + Number(res.tax_dipungut_negara.taxAmt);
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
                    var newRow4 = $('<tr>');
                    newRow4.append($('<td style="text-align:right;" colspan="6">').text('Pajak Dipungut Negara (' + res.tax_dipungut_negara.taxType + ')'));
                    newRow4.append($('<td>').text(formatRupiah(res.tax_dipungut_negara.taxAmt)));
                    table.find('tbody').append(newRow4);
                    var newRow5 = $('<tr>');
                    newRow5.append($('<td style="text-align:right;" colspan="6">').text('Pajak Dikembalikan Lagi (' + res.tax_dikembalikan_lagi.taxType + ')'));
                    newRow5.append($('<td>').text(formatRupiah(res.tax_dikembalikan_lagi.taxAmt)));
                    table.find('tbody').append(newRow5);
                    var newRow6 = $('<tr>');
                    newRow6.append($('<td style="text-align:right;" colspan="6">').text('Sub Total'));
                    newRow6.append($('<td>').text(formatRupiah(subTotal)));
                    table.find('tbody').append(newRow6);
                }
            })
        });

        $('#supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function(e) {
            table.find('tbody').empty();
            $("#tanda_terima_supplier").empty();
            $.ajax({
                url: '<?= base_url('pembayaran-po-lokal-bp/get-rekap-faktur/') ?>' + $(this).val(),
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
                }
            });
        });

        $(".btn-submit-form").click(function() {
            var id = $('.id').val();
            const csrfToken = '<?= csrf_token() ?>';
            const csrf = $(`[name="${csrfToken}"]`);
            if (id) {
                // UPDATE

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
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const data = new FormData(document.querySelector(".create-form"));
                            $.ajax({
                                url: "<?= base_url("pembayaran-po-lokal-bp/create"); ?>",
                                data: data,
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("type", "Bahan Penolong");
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
</script>
<script>
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
</script>

<?= $this->endSection(); ?>