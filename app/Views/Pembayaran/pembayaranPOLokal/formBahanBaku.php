<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("pembayaran-po-lokal-bp"); ?>">
                Batal
            </a>
            <?php if (!empty($detail)) : ?>
                <a class="btn btn-warning btn-print float-right text-white" target="_blank" href="<?= base_url('pembayaran-po-lokal-bp/print/' . $detail['pembayaranDetail']['id'] ?? '') ?>">
                    <i class="fa-solid fa-print"></i> Print
                </a>
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
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker due_date" id="payment_date" name="payment_date" placeholder="Tanggal Jatuh Tempo" <?= !empty($detail) ? 'disabled value="' . $detail['pembayaranDetail']['payment_date'] . '"' : '' ?>>
                            <label for="floatingInput">Tanggal Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" <?= !empty($detail) ? 'disabled' : '' ?> name="supplier_id" id="supplier_id">
                                <option disabled selected value=""></option>
                                <?php foreach ($suppliers as $supplier) : ?>
                                    <option <?= !empty($detail) ? ($detail['pembayaranDetail']['supplier_id'] == $supplier->id ? 'selected' : '') : '' ?> value="<?= $supplier->id ?>"><?= $supplier->name ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <select class="form-select" name="tipe_pembayaran" id="tipe_pembayaran">
                                <option disabled selected value=""></option>
                                <option value="BULANAN">BULANAN</option>
                                <option value="HARIAN">HARIAN</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Bayar</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                            <input type="text" name="jenis_dokumen" class="form-control" id="jenis_dokumen" readonly>
                            <label for="floatingInput" style="z-index: 1;">Jenis Dokumen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bulanan-form">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <input type="month" name="bulan" id="bulan" class="form-control">
                                <label for="floatingInput" style="z-index: 1;">Pilih Bulan</label>
                            </div>
                        </div>
                        <div class="harian-form">
                            <div class="form-floating form-pembayaran-po mb-3" style="height: 50px;">
                                <select class="form-select" name="lpb" id="lpb">
                                    <option disabled selected value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">No Dokumen LPB</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" onkeyup="this.value = formatRupiah(this.value);" type="text" class="form-control nominal_pembayaran" name="nominal_pembayaran" id="nominal_pembayaran" readonly <?= !empty($detail) ? 'disabled value="' . "Rp " . number_format($detail['pembayaranDetail']['amount'], 2, ',', '.')  . '"' : '' ?>>
                            <label for="floatingInput">Nominal Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" class="form-control input-picker jatuh_tempo" id="jatuh_tempo" name="jatuh_tempo" placeholder="Tanggal Jatuh Tempo" readonly <?= !empty($detail) ? 'value="' . date('d/m/Y', strtotime($detail['pembayaranDetail']['due_date']))  . '"' : '' ?>>
                            <label for="floatingInput">Tanggal Jatuh Tempo</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($detail) ? 'disabled' : '' ?> class="form-select " name="payment_method" id="payment_method">
                                <option disabled selected value=""></option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Cash" ? 'selected' : '') : '' ?> value="Cash">Cash</option>
                                <option <?= !empty($detail) ? ($detail['pembayaranDetail']['payment_method'] == "Debit" ? 'selected' : '') : '' ?> value="Debit">Debit</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Metode Pembayaran</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input name="pembayaran_oleh" autocomplete="one-time-code" value="<?= !empty($detail) ? $detail['pembayaranDetail']['pembayaran_oleh'] : session()->get("login")->name; ?>" type="text" readonly="true" class="form-control" placeholder="Pembayaran Oleh">
                            <label for="floatingInput">Pembayaran Oleh</label>
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
                                        <th style="text-align: center;">Tanggal PO</th>
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Barang</th>
                                        <th style="text-align: center;">Total Order</th>
                                        <th style="text-align: center;">Total Diterima</th>
                                        <th style="text-align: center;">Total Harga</th>
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
                                                <td><?= "Rp " . number_format($d['price'], 2, ',', '.')  ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">
                                                Total
                                            </td>
                                            <td><?= "Rp " . number_format($detail['tandaTerimaSupplier']['nominal_faktur'], 2, ',', '.')  ?></td>
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
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            errorPlacement: function(error, element) {
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

        $('.bulanan-form,.harian-form').hide();

        $('#no_dokumen, #lpb').select2({
            placeholder: "",
            theme: "bootstrap-5"
        });

        $('#tipe_pembayaran').select2({
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function() {
            var tipeBayar = $(this).val();
            if (tipeBayar == "BULANAN") {
                $('.bulanan-form').show();
                $('.harian-form').hide();
                $('#jenis_dokumen').val("KWITANSI TB");
            } else {
                $('.harian-form').show();
                $('.bulanan-form').hide();
                $('#jenis_dokumen').val("LPB");
                generateLPBNo();
            }
        });

        $('#supplier_id').select2({
            placeholder: "",
            theme: "bootstrap-5"
        }).change(function(e) {
            if ($('#tipe_pembayaran').val() == "HARIAN") {
                generateLPBNo();
            }
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
    });

    function generateLPBNo() {
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("supplierID", $('#supplier_id').val());

        $.ajax({
            url: "<?= base_url("pembayaran-po-lokal-bb/get-lpb-not-paid"); ?>",
            data: formData,
            method: "POST",
            dataType: "json",
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
            },
            processData: false,
            contentType: false,
            success: function(response) {
                csrf.val(response.token);
                $("#lpb").empty();
                $("#lpb").append(`<option value=""></option>`);
                response.data.forEach(function(item) {
                    $("#lpb").append(`<option  value="${item.lpbID}">${item.lpbNO}</option>`);
                });

            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        const csrfToken = '<?= csrf_token() ?>';
        const csrf = $(`[name="${csrfToken}"]`);
        var formData = new FormData();
        formData.append("type", "Bahan Baku");
        if (value) {
            $(".no_bukti_pembayaran").attr("readonly", true);
            $.ajax({
                url: "<?= base_url("pembayaran-po-lokal-bp/generate-no-pembayaran"); ?>",
                data: formData,
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
        return "Rp. " + ribuanFormatted + ',' + desimal;
    }
</script>

<?= $this->endSection(); ?>