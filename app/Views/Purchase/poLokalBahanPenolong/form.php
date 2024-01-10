<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Tambah PO Lokal Bahan Penolong</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("po-lokal-bahan-penolong"); ?>">
                Batal
            </a>
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    <button class="btn btn-hapus delete-parent float-right">
                        Hapus
                    </button>
                <?php endif ?>
                <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("po-lokal-bahan-penolong/print/"); ?><?= $poDetail['id'] ?>')">
                    Print
                </button>
                <?php if (!$poDetail['is_posted']) : ?>
                    <button class="btn btn-success posting-spp float-right posting-po">
                        Posting
                    </button>
                <?php endif ?>
                <?php if ($poDetail['is_posted']) : ?>
                    <?php if (!$poDetail['status_penerimaan']) : ?>
                        <button class="btn btn-hapus close-parent float-right">
                            Close PO
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif ?>
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                        Simpan
                    </button>
                <?php endif; ?>
            <?php else : ?>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data PO</label>
                </div>
            </div>
            <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" <?= !empty($poDetail) ? 'value="' . $poDetail['id'] . '"' : '' ?> />
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" class="form-control input-picker po_date" id="po_date" name="po_date" placeholder="Tanggal Dibuat" <?= !empty($poDetail) ?  'readonly value="' . formatYMDtoDMY($poDetail['po_date']) . '"' : 'value="' . formatYMDtoDMY($today) . '"' ?>>
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" <?= !empty($dataPOLokal) ? ($dataPOLokal->is_posted === "1" ? 'disabled=true' : '') : ''; ?> type="text" class="form-control po_no" id="po_no" name="po_no" placeholder="No. PO" <?= !empty($poDetail) ?  'readonly value="' . $poDetail['po_no'] . '"' : '' ?>>
                                    <label for="floatingInput">No. PO</label>
                                </div>
                                <div style="<?= !empty($poDetail) ? 'display:none;' : '' ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 25px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select company_id" id="company_id" name="company_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($company as $c) : ?>
                                    <option <?= !empty($poDetail) ? (($poDetail['company_id'] == $c['id']) ? 'selected' : '') : '' ?> value="<?= $c['id'] ?>"><?= strtoupper($c['company']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Company</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select division_id" id="division_id" name="division_id" aria-label="Floating label select example">
                                <option value=""></option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> class="form-select supplier_id" id="supplier_id" name="supplier_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($supplier as $s) : ?>
                                    <option value="<?= $s['id'] ?>">
                                        <?= strtoupper($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($poDetail) ? formatYMDtoDMY($poDetail['payment_date']) : formatYMDtoDMY($today) ?>" class="form-control input-picker payment_date" id="payment_date" name="payment_date" placeholder="Tanggal Pembayaran">
                                    <label for="floatingInput">Tanggal Pembayaran</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 25px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-payment-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($poDetail) ? ($poDetail['is_posted'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" value="<?= !empty($poDetail) ? $poDetail['note'] : '' ?>" type="text" class="form-control note" id="note" name="note" placeholder="Catatan (Opsional)">
                            <label for="floatingInput">Catatan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                </div>
            </div>
            <form class="detail-form" role="form" method="POST" enctype="multipart/form-data" style="<?= !empty($poDetail) ? ($poDetail['is_posted'] ? "display: none;" : "") : ""; ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select barang_id" id="barang_id" name="barang_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($barang as $s) : ?>
                                    <option data-parent_name="<?= $s['parent_name'] ?>" data-satuan_id="<?= $s['satuan_id'] ?>" data-nama_barang="<?= strtoupper($s['barang_name']) ?>" data-kode_barang="<?= $s['kode_barang'] ?>" value="<?= $s['id'] ?>">
                                        <?= strtoupper($s['kode_barang']) . " ( " . strtoupper($s['parent_name']) . " ) ( " . strtoupper($s['barang_name']) . " )" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Kode Barang</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="hidden" name="barang_update_id" id="barang_update_id" class="barang_update_id">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control nama_barang" id="nama_barang" name="nama_barang">
                            <label for="floatingInput">Nama Barang</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" readonly type="text" class="form-control nama_kategori" id="nama_kategori" name="nama_kategori">
                            <label for="floatingInput">Nama Kategori</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select disabled class="form-select satuan_id" name="satuan_id" id="satuan_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($satuan as $s) : ?>
                                    <option data-nama_satuan="<?= strtoupper($s['nama_satuan']) ?>" value="<?= $s['id'] ?>">
                                        <?= strtoupper($s['nama_satuan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Satuan</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control harga_satuan" name="harga_satuan" id="harga_satuan" placeholder="Harga Satuan">
                            <label for="floatingInput">Harga Satuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control qty" name="qty" id="qty" placeholder="Qty">
                            <label for="floatingInput">QTY</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" min="0" max="100" type="number" class="form-control diskon" required value="0" name="diskon" id="diskon" placeholder="Discount (%)">
                            <label for="floatingInput">Diskon (%)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="number" class="form-control biaya_tambahan" name="biaya_tambahan" id="biaya_tambahan" placeholder="Biaya Tambahan">
                            <label for="floatingInput">Biaya Tambahan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" readonly class="form-control total" name="total" id="total" placeholder="Total">
                            <label for="floatingInput">Total</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control keterangan" name="keterangan" id="keterangan" placeholder="Keterangan">
                            <label for="floatingInput">Keterangan (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select ppn" name="ppn" id="ppn" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($ppn as $p) : ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih PPN (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select pph" name="pph" id="pph" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($pph as $p) : ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Pilih PPH (Opsional)</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal" style="<?= !empty($poDetail) ? ($poDetail['is_posted'] ? "display: none;" : "") : ""; ?>">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right btn-submit-detail">
                            <i class="fa fa-plus fa-sm mr-1" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetForm()">
                            <i class="fa-solid fa-rotate-right mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="text-align: center; width:10px">No.</th>
                                <th style="text-align: center;">Kode</th>
                                <th style="text-align: center;">Barang</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Harga Satuan</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: center;">Diskon (%)</th>
                                <th style="text-align: center;">Biaya Tambahan</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody style="text-align:center;">
                        </tbody>
                        <tfoot class="foot-detail-table" id="foot-detail-table">
                            <tr>
                                <td style="text-align: right;" colspan="8">
                                    <b>TOTAL</b>
                                </td>
                                <td style="text-align: center;">
                                    <b>0.00</b>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);
    // init barang list
    var listBarang = [];
    var totalHarga = 0;
    // init select2
    $('#company_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {
        var companyID = $(this).val();
        var formData = new FormData();
        formData.append("companyID", companyID);
        $.ajax({
            url: "<?= base_url("po-lokal-bahan-penolong/find-divisi"); ?>",
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
                $("#division_id").empty();
                $("#division_id").append(`<option value=""></option>`);
                response.data.forEach(function(item) {
                    $("#division_id").append(`<option  value="${item.id}">${item.divisi}</option>`);
                });
                <?php if (!empty($poDetail)) : ?>
                    $('#division_id').val("<?= $poDetail['division_id'] ?>").change();
                <?php endif; ?>
            }
        });
    });

    $('#division_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {

    });

    $('#supplier_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {

    });

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#barang_id').select2({
        placeholder: "",
        theme: "bootstrap-5"
    }).change(function() {
        $('#nama_barang').val($(this).find("option:selected").data("nama_barang"));
        $('#nama_kategori').val($(this).find("option:selected").data("parent_name"));
        $('#satuan_id').val(($(this).find("option:selected").data("satuan_id")));

        $.ajax({
            url: "<?= base_url("po-lokal-bahan-penolong/histori-harga"); ?>",
            data: {
                id: $('#barang_id').val()
            },
            method: "GET",
            success: function(response) {
                if (response.hargaTerakhir !== "-") {
                    $('#harga_satuan').val(formatCurrency(response.res.hargaTerakhir));
                } else {
                    $('#harga_satuan').val(formatCurrency('0'));
                }
            },
        });
    });

    $("#po_date,#payment_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    // HARGA SATUAN DAN QTY CHANE
    $('#harga_satuan,#qty,#biaya_tambahan,#diskon').keyup(function() {
        var hargaSatuan = parseInt($('#harga_satuan').val()) || 0;
        var qty = parseInt($('#qty').val()) || 1;
        var biayaTambahan = parseInt($('#biaya_tambahan').val()) || 0;
        var diskon = parseInt($('#diskon').val()) || 0;
        var diskonHarga = (diskon / 100) * (hargaSatuan * qty);
        console.log(diskonHarga);

        var total = (((hargaSatuan * qty) - diskonHarga) + biayaTambahan);
        $('#total').val(formatRupiah(total));
    });

    // VALIDATOR DETAIL
    var validatorBarang = $(".detail-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            nama_barang: {
                required: true
            },
            satuan_id: {
                required: true
            },
            harga_satuan: {
                required: true
            },
            qty: {
                required: true,
            },
            diskon: {
                required: true,
            },
            total: {
                required: true,
            }
        },
        messages: {
            barang_id: {
                required: "Pilih Kode Barang"
            },
            nama_barang: {
                required: "Nama barang wajib diisi"
            },
            satuan_id: {
                required: "Satuan wajib diisi"
            },
            harga_satuan: {
                required: "Harga Satuan wajib diisi"
            },
            qty: {
                required: "Kuantitas barang wajib diisi"
            },
            total: {
                required: "Total biaya wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    var validatorPO = $(".create-form").validate({
        rules: {
            po_date: {
                required: true
            },
            po_no: {
                required: true
            },
            company_id: {
                required: true
            },
            division_id: {
                required: true
            },
            supplier_id: {
                required: true,
            },
            payment_date: {
                required: true,
            }
        },
        messages: {
            po_date: {
                required: "Tanggal PO Dibuat wajib diisi"
            },
            po_no: {
                required: "Nomor PO wajib diisi"
            },
            company_id: {
                required: "Pilih unit company"
            },
            division_id: {
                required: "Pilih departemen"
            },
            supplier_id: {
                required: "Pilih supplier"
            },
            payment_date: {
                required: "Tanggal pembayaran wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });

    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
            var barang_update_id = $('#barang_update_id').val();
            if (barang_update_id != "") {
                // UPDATE
                Swal.fire({
                    icon: 'question',
                    title: 'Update barang ?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log(barang_update_id);
                        var indexToRemove = -1;
                        for (var i = 0; i < listBarang.length; i++) {
                            if (listBarang[i].barang_id === barang_update_id) {
                                indexToRemove = i;
                                break;
                            }
                        }
                        if (indexToRemove !== -1) {
                            listBarang.splice(indexToRemove, 1);
                            insertList();
                            resetForm();
                        }
                    }
                });
            } else {
                // TAMBAH
                var isAdd = false;
                for (var i = 0; i < listBarang.length; i++) {
                    if (listBarang[i].barang_id === $('#barang_id').val()) {
                        indexToRemove = i;
                        isAdd = true;
                        break;
                    }
                }
                if (isAdd) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang Sudah Ada',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Tambah barang ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            insertList();
                        }
                    })
                }

            }

        }
    });

    $('.btn-submit-parent').click(function() {
        if ($('.create-form').valid()) {
            if (listBarang.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang masih kosong',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                var id = $('#id').val();
                if (id) {
                    // UPDATE
                    Swal.fire({
                        icon: 'question',
                        title: 'Update Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var id = $('#id').val();
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var companyID = $('#company_id').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var note = $('#note').val();
                            // append
                            var formData = new FormData();
                            formData.append("id", id);
                            formData.append("poDate", poDate);
                            formData.append("poNo", poNo);
                            formData.append("companyID", companyID);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("total", totalHarga);
                            formData.append("note", note);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-lokal-bahan-penolong/update"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            location.reload();
                                        })
                                    }

                                }
                            });
                        }
                    })
                } else {
                    // CREATE
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var poDate = $('#po_date').val();
                            var poNo = $('#po_no').val();
                            var companyID = $('#company_id').val();
                            var divisionID = $('#division_id').val();
                            var supplierID = $('#supplier_id').val();
                            var paymentDate = $('#payment_date').val();
                            var note = $('#note').val();
                            // append
                            var formData = new FormData();
                            formData.append("poDate", poDate);
                            formData.append("poNo", poNo);
                            formData.append("companyID", companyID);
                            formData.append("divisionID", divisionID);
                            formData.append("supplierID", supplierID);
                            formData.append("paymentDate", paymentDate);
                            formData.append("total", totalHarga);
                            formData.append("note", note);
                            formData.append("listBarang", JSON.stringify(listBarang));

                            $.ajax({
                                url: "<?= base_url("po-lokal-bahan-penolong/save"); ?>",
                                data: formData,
                                method: "POST",
                                dataType: "json",
                                beforeSend: function(xhr) {
                                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                },
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            reverseButtons: true,
                                            confirmButtonText: 'Oke',
                                        }).then((result) => {
                                            window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>"

                                        })
                                    }

                                }
                            });
                        }
                    });
                }
            }
        }
    });

    function insertList() {
        listBarang.push({
            barang_id: $('#barang_id').val(),
            kode_barang: $('#barang_id').find("option:selected").data("kode_barang"),
            nama_barang: $('#nama_barang').val(),
            satuan_id: $('#satuan_id').val(),
            nama_satuan: $('#satuan_id').find("option:selected").data("nama_satuan"),
            harga_satuan: $('#harga_satuan').val(),
            qty: $('#qty').val(),
            diskon: $('#diskon').val(),
            harga_satuan: $('#harga_satuan').val() || 0,
            biaya_tambahan: $('#biaya_tambahan').val() || 0,
            total: $('#total').val(),
            keterangan: $('#keterangan').val(),
            ppn: $('#ppn').val(),
            pph: $('#pph').val()
        });
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
        // reset update flag
        $('#barang_update_id').val("")
        drawTabel(listBarang);
        console.log(listBarang);
    }

    function drawTabel(listBarang) {
        const table = $('#dataTable');
        var no = 1;
        table.find('tbody').empty();
        totalHarga = 0;
        $.each(listBarang, function(i, v) {
            var newRow = $('<tr>');
            newRow.append($('<td style="text-align:center;">').text(no++));
            newRow.append($('<td>').text(v.kode_barang));
            newRow.append($('<td>').text(v.nama_barang));
            newRow.append($('<td>').text(v.nama_satuan));
            newRow.append($('<td>').text(formatRupiah(v.harga_satuan)));
            newRow.append($('<td>').text(v.qty));
            newRow.append($('<td>').text(v.diskon));
            newRow.append($('<td>').text(formatRupiah(v.biaya_tambahan)));
            newRow.append($('<td>').text(v.total));
            <?php if (!empty($poDetail)) : ?>
                <?php if (!$poDetail['is_posted']) : ?>
                    newRow.append($('<td>').html(
                        `
                            <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.barang_id}')">
                                <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                            </button><button class="btn btn-danger" onclick="deleteRow('${v.barang_id}')">
                                <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                            </button>
                        `
                    ));
                <?php else : ?>
                    newRow.append($('<td>').text('-'));
                <?php endif; ?>
            <?php else : ?>
                newRow.append($('<td>').html(
                    `
                        <button class="btn btn-warning posting-spp mr-1" onclick="detailRow('${v.barang_id}')">
                            <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                        </button><button class="btn btn-danger" onclick="deleteRow('${v.barang_id}')">
                            <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                        </button>
                    `
                ));
            <?php endif; ?>

            table.find('tbody').append(newRow);
            totalHarga += parseInt(formatCurrency(v.total));
        });
        table.find('tfoot').empty();
        var newRow = $('<tr>');
        newRow.append($('<td style="text-align:right;" colspan="8"><b>Total</b></td>'));
        newRow.append($('<td style="text-align:center;"><b>' + formatRupiah(totalHarga) + '</b></td>'));
        newRow.append($('<td></td>'));
        table.find('tfoot').append(newRow);
    }

    // generate no po
    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".po_no").attr("readonly", true);
            $(".po_no").val("AUTO GENERATE");
        } else {
            $(".po_no").attr("readonly", false);
            $(".po_no").val("");
        }
    }

    function deleteRow(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Barang ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                var indexToRemove = -1;
                for (var i = 0; i < listBarang.length; i++) {
                    if (listBarang[i].barang_id === id) {
                        indexToRemove = i;
                        break;
                    }
                }
                if (indexToRemove !== -1) {
                    listBarang.splice(indexToRemove, 1);
                }
                drawTabel(listBarang);
                resetForm();
            }
        })

    }

    function detailRow(id) {
        var item = null;
        for (var i = 0; i < listBarang.length; i++) {
            if (listBarang[i].barang_id === id) {
                item = listBarang[i];
                break;
            }
        }
        $('#barang_id, #barang_update_id').val(item.barang_id).change();
        $('#harga_satuan').val(item.harga_satuan);
        $('#qty').val(item.qty);
        $('#diskon').val(item.diskon);
        $('#biaya_tambahan').val((item.biaya_tambahan == 0) ? "" : item.biaya_tambahan).val();
        $('#keterangan').val(item.keterangan);
        $('#ppn').val(item.ppn);
        $('#pph').val(item.pph);
        $('#biaya_tambahan').change();
        $('#total').val(item.total);
        // attr barang_id disabled
        $('#barang_id').attr('disabled', true);
        console.log(item);
    }

    function resetForm() {
        $('#barang_id').attr('disabled', false);
        $(".detail-form input, .detail-form select").val("");
        $(".barang_id").val("").change();
        $(".diskon").val('0');
    }

    function formatCurrency(str) {
        var strs = str.replace(/,..$/, '');
        return strs.replace(/[^0-9]/g, '');
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
</script>
<!-- Edit Script -->
<?php if (!empty($poDetail)) : ?>
    <script>
        $('#company_id').change();
        $('#supplier_id').val("<?= $poDetail['supplier_id'] ?>").change();
        <?php foreach ($listBarang as $l) : ?>
            listBarang.push({
                barang_id: "<?= $l['barang_id'] ?>",
                kode_barang: "<?= $l['kode_barang'] ?>",
                nama_barang: "<?= $l['nama_barang'] ?>",
                satuan_id: "<?= $l['satuan_id'] ?>",
                nama_satuan: "<?= $l['nama_satuan'] ?>",
                harga_satuan: "<?= $l['harga_satuan'] ?>",
                qty: "<?= $l['qty'] ?>",
                diskon: "<?= $l['diskon'] ?>",
                harga_satuan: "<?= $l['harga_satuan'] ?>",
                biaya_tambahan: "<?= $l['biaya_tambahan'] ?>",
                total: formatRupiah("<?= (($l['harga_satuan'] * $l['qty']) - (($l['diskon'] / 100) * ($l['harga_satuan'] * $l['qty']))) + $l['biaya_tambahan'] ?>"),
                keterangan: "<?= $l['keterangan'] ?>",
                ppn: "<?= $l['ppn'] ?>",
                pph: "<?= $l['pph'] ?>"
            })
        <?php endforeach; ?>
        drawTabel(listBarang);

        // HAPUS
        $('.delete-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan hapus PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/delete"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    window.location.href = "<?= base_url('po-lokal-bahan-penolong') ?>"
                                })
                            }

                        }
                    });
                }
            })
        });

        //POSTING
        $('.posting-po').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan posting PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/update-status"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    location.reload();
                                })
                            }

                        }
                    });
                }
            })
        });

        // CLOSE PO
        $('.close-parent').click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Yakin akan close PO ?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var id = $('#id').val();
                    formData.append("id", id);
                    $.ajax({
                        url: "<?= base_url("po-lokal-bahan-penolong/close-po"); ?>",
                        data: formData,
                        method: "POST",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                    reverseButtons: true,
                                    confirmButtonText: 'Oke',
                                }).then((result) => {
                                    location.reload();
                                })
                            }

                        }
                    });
                }
            })
        });
        const print = function(url) {
            window.open(url, "_blank");
        }
    </script>
<?php endif; ?>

<?= $this->endSection(); ?>