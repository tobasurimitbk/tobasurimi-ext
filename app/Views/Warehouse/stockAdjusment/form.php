<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Tambah Stok Adjusment</h1>
        <?php if (can("Inventori", "Stok List", "c")) : ?>
            <div class="col-button-tambah-spp">
                <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                    Batal
                </a>
                <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                    Simpan
                </button>
            </div>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Adjusment</label>
                </div>
            </div>
            <form class="create-form">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime($tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 20px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($dataStockAdjusment) ? ($dataStockAdjusment['status_posting'] === "FINISH" ? 'disabled=true' : '') : ''; ?> value="<?= !empty($dataStockAdjusment) ? $dataStockAdjusment['no_penerimaan_barang'] : "ADJ//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_penerimaan_barang" id="no_penerimaan_barang" name="no_penerimaan_barang" placeholder="No. Penerimaan">
                                    <label for="floatingInput">No. Penerimaan</label>
                                </div>
                                <div style="<?= !empty($dataStockAdjusment) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="Keterangan" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Data Barang</label>
                </div>
            </div>
            <form class="detail-form">
                <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select type_barang" id="type_barang" name="type_barang" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($tipeBarang as $t) : ?>
                                    <option value="<?= $t['description'] ?>">
                                        <?= strtoupper($t['value']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating" style="height: 50px;">
                            <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi</label>
                        </div>
                        <small class="mb-3"><i>Hanya menampilkan barang yang sudah diinisiasi stok awal-nya</i></small>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="" class="form-control satuan_name" disabled id="satuan_name" name="satuan_name" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Satuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bc_id" id="bc_id" name="bc_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($jenisDokAju as $j) : ?>
                                    <option value="<?= $j->id ?>">
                                        <?= $j->value ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="0">NON PABEAN</option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Dokumen Pabean</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select no_aju" id="no_aju" name="no_aju" aria-label="Floating label select example">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Nomor Aju</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select operasi" id="operasi" name="operasi" aria-label="Floating label select example">
                                <option value=""></option>
                                <option value="PLUS">PLUS</option>
                                <option value="MINUS">MINUS</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Operasi</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input placeholder="Qty" class="form-control qty" id="qty" name="qty" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Qty</label>
                        </div>
                    </div>
                </div>
            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Barang</label>
                    </div>
                    <div class="col-md-6">

                        <button class="btn btn-show-detail btn-add btn-block btn-submit-detail float-right" data-btn="detail-modal">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                        <button style="border-color: #e7323a !important; background-color: #e7323a !important; margin-right: 10px !important;" class="btn btn-add btn-block float-right" onclick="resetFormDetail()">
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
                                <th>No</th>
                                <th>Tipe Barang</th>
                                <th>Kode</th>
                                <th>Barang - Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Warehouse</th>
                                <th>Dokumen</th>
                                <th>Operasi</th>
                                <th>QTY</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10" style="text-align: center;">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    var listStock = [];
    var qtyTotal = 0;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-adjusment/warehouse'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                divisi_id: $(".divisi_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_id").empty()
                $(".warehouse_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
                $(".warehouse_id").val();
            }
        });

        listStock = [];
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
    });


    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#spesifikasi_id option:selected');
        $('#satuan_name').val(selected.data('kode_satuan'));
    });

    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var bc_id = $(this).val();
        if (bc_id == '0') {
            $('#no_aju').val(null).change();
            $('#no_aju').attr('disabled', true);
        } else {
            $('#no_aju').attr('disabled', false);
        }
    });


    $('#no_aju').select2({
        placeholder: "Pilih Nomor Aju",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('#operasi').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true
    });

    // VALIDATOR DETAIL
    var validatorDetail = $(".detail-form").validate({
        rules: {
            type_barang: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            spesifikasi_id: {
                required: true
            },
            bc_id: {
                required: true
            },
            operasi: {
                required: true
            },
            qty: {
                required: true,
                number: true,
                min: -1
            },
        },
        messages: {
            type_barang: {
                required: "Pilih tipe barang"
            },
            warehouse_id: {
                required: "Pilih warehouse"
            },
            spesifikasi_id: {
                required: "Pilih barang"
            },
            bc_id: {
                required: "Pilih dokumen pabean"
            },
            operasi: {
                required: "Pilih operasi"
            },
            qty: {
                required: "Qty wajib diisi",
                number: "Qty harus berupa angka",
                min: "Qty harus diisi lebih dari 0"
            },
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


    $("#type_barang,#divisi_id,#warehouse_id,#spesifikasi_id,#bc_id,#no_aju,#operasi")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // SUBMIT DETAIL
    $('.btn-submit-detail').click(function() {
        if ($('.detail-form').valid()) {
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

                }
            })

        }
    });

    function drawTable() {
        const table = $('#dataTable');
        table.find('tbody').empty();
        var no = 1;
        if (listStock.length == 0) {

        } else {
            var no = 1;
            $.each(listStock, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.type_barang));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang));
                newRow.append($('<td>').text(v.kode_satuan));
                newRow.append($('<td>').text(v.jml_order));
                newRow.append($('<td>').text(v.jml_diterima_lpb));
                newRow.append($('<td>').text(v.jml_diterima_total));
                newRow.append($('<td>').text(v.sisa_total));
                newRow.append($('<td>').text(formatRupiah(parseFloat(v.harga) || 0)));
                newRow.append($('<td>').text(formatRupiah(parseFloat(v.sub_total) || 0)));
                newRow.append($('<td>').text(v.keterangan));
                newRow.append($('<td>').html(
                    <?php if (!empty($dataStockAdjusment)) : ?> <?php if ($dataStockAdjusment['status_posting'] === "FINISH") : ?> `-`
                        <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="editDetail('${v.id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                `
                        <?php endif; ?> <?php else : ?> `
                <button class="btn btn-warning posting-spp mr-1" onclick="deleteDetail('${v.id}')">
                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                </button>
                `
                    <?php endif; ?>
                ));
                table.find('tbody').append(newRow);

            });
        }

    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('stock-adjusment/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                type_barang: $(".type_barang option:selected").val(),
                divisi_id: $(".divisi_id option:selected").val(),
                warehouse_id: $(".warehouse_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    function resetFormDetail() {
        $('#id_detail').val(null);
        $('#type_barang').val(null).change();
        $('#warehouse_id').val(null).change();
        $('#spesifikasi_id').val(null).change();
        $('#bc_id').val(null).change();
        $('#no_aju').val(null).change();
        $('#operasi').val(null).change();
        $('#qty').val(null);
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