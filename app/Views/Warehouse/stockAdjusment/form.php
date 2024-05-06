<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($adjusment) ? "Tambah Stok Adjusment" : "Update Stok Adjusment" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-adjusment"); ?>">
                Batal
            </a>
            <?php if (!empty($adjusment)) : ?>
                <?php if ($adjusment['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-adjusment">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Stok Adjusment', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
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
                    <label class="form-label font-weight-bold lable-title">Data Adjusment</label>
                </div>
            </div>
            <form class="create-form">
                <input type="hidden" name="id" id="id" value="<?= !empty($adjusment) ? encrypt($adjusment['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input autocomplete="one-time-code" disabled class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($adjusment) ? $adjusment['tanggal'] : $tanggal)); ?>">
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
                                    <input readonly autocomplete="one-time-code" <?= !empty($adjusment) ? 'disabled=true' : ''; ?> value="<?= !empty($adjusment) ? $adjusment['no_adjusment'] : "ADJ//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_adjusment" id="no_adjusment" name="no_adjusment" placeholder="No. Adjusment">
                                    <label for="floatingInput">No. Adjusment</label>
                                </div>
                                <div style="<?= !empty($adjusment) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select tipe_adjusment" id="tipe_adjusment" name="tipe_adjusment" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($tipeAdjusment as $t) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['tipe_adjusment'] == $t['value'] ? 'selected' : '') : '' ?> value="<?= $t['value'] ?>">
                                        <?= $t['value']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Adjusment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($adjusment) ? ($adjusment['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                <option <?= !empty($adjusment) ? ($adjusment['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($adjusment) ? $adjusment['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-component">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Pilih Barang Yang Akan Di Adjusment</label>
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
                                    <?php if (!empty($warehouse)) : ?>
                                        <?php foreach ($warehouse as $w) : ?>
                                            <option value="<?= $w['id'] ?>">
                                                <?= $w['warehouse_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-fifo">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select operasi_fifo" id="operasi_fifo" name="operasi_fifo" aria-label="Floating label select example">
                                        <option value=""></option>
                                        <option value="PLUS">PENAMBAHAN STOK</option>
                                        <option value="MINUS">PENGURANGAN STOK</option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Operasi</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty_adjusment_fifo" id="qty_adjusment_fifo" name="qty_adjusment_fifo" aria-label="Floating label select example" />
                                    <label for="floatingInput" style="z-index: 1;">Qty Adjusment</label>
                                </div>
                            </div>
                        </div>


                    </div>
                </form>

                <div class="detail-form-layout">
                    <div class="row mt-3">
                        <div class="col mb-0">
                            <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang Yang Akan Di Adjusment</label>
                        </div>
                        <div class="col-md-12 col-table-button-tts" style="margin-top: 10px;">
                            <div class="table-responsive">
                                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-inventori" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">Asal Barang</th>
                                            <th style="text-align: center;">No Dokumen</th>
                                            <th style="text-align: center;">Supplier</th>
                                            <th style="text-align: center;">Dokumen Pabean</th>
                                            <th style="text-align: center;">No Aju</th>
                                            <th style="text-align: center;">Tanggal Penerimaan</th>
                                            <th style="text-align: center;">Barang - Spesifikasi</th>
                                            <th style="text-align: center;">Satuan</th>
                                            <th style="text-align: center;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody class="body-table" id="body-detail-list-inventori">

                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-subtitle-modal">
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold modal-sub-title">List Adjusment Barang</label>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-adjusment" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Tipe Barang</th>
                                <th>Asal Barang</th>
                                <th>No Dokumen</th>
                                <th>Supplier</th>
                                <th>Kode</th>
                                <th>Barang - Spesifikasi</th>
                                <th>Satuan</th>
                                <th>Warehouse</th>
                                <th>Dokumen</th>
                                <th>Qty</th>
                                <th>Tipe Adjusment</th>
                                <th>Qty Adjusment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="14" style="text-align: center;">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    var listStockInventori = [];
    var listStock = [];
    var qtyTotal = 0;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('.form-fifo').hide();

    <?php if (!empty($adjusment)) : ?>
        <?php if ($adjusment['status_posting']) : ?>
            $('.detail-form-component').hide();
        <?php endif; ?>
        <?php foreach ($listBarang as $l) : ?>
            listStock.push({
                id: "<?= $l['id'] ?>",
                stock_id: "<?= $l['stock_id'] ?>",
                spesifikasi_id: "<?= $l['spesifikasi_id'] ?>",
                sumber: "<?= $l['sumber'] ?>",
                stock_dokumen: "<?= $l['stock_dokumen'] ?>",
                supplier_name: "<?= $l['supplier_name'] ?>",
                bc_id: "<?= $l['bc_id'] ?>",
                no_aju: "<?= $l['no_aju'] ?>",
                dokumen_text: "<?= $l['dokumen_text'] ?>",
                barang: "<?= $l['barang'] ?>",
                kode_barang: "<?= $l['kode_barang'] ?>",
                type_barang: "<?= $l['type_barang'] ?>",
                type_barang_text: "<?= $l['type_barang_text'] ?>",
                satuan: "<?= $l['satuan'] ?>",
                warehouse_text: "<?= $l['warehouse_text'] ?>",
                warehouse_id: "<?= $l['warehouse_id'] ?>",
                stok_total: "<?= $l['stok_total'] ?>",
                type_adjusment: "<?= $l['type_adjusment'] ?>",
                qty_adjusment: "<?= $l['qty_adjusment'] ?>",
            })
        <?php endforeach; ?>
        drawTable();
    <?php endif; ?>

    var dataTableListStock = $('.table-inventori').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: true,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: true,
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });


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
        // GET NO ADJUSMENT
        changeStatus();
        listStock = [];
        drawTable();
    });

    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.form-fifo').show();
        } else {
            $('.form-fifo').hide();
        }
        listStockInventori = [];
        listStock = [];
        drawTable();
        drawTableListInventori();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
        listStockInventori = [];
        drawTableListInventori();
    });

    $('#tipe_adjusment').select2({
        placeholder: "Pilih Tipe Adjusment",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#operasi_fifo').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});


    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        getListBarang();
        listStockInventori = [];
        drawTableListInventori();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang (Hanya Barang Yang Sudah Inisiasi Stok)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#spesifikasi_id').change(function() {
        getListDokumenPabean();
    });

    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Pabean",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        var selected = $('#bc_id option:selected');
        var satuan_name = $('#satuan_name').val();
        var stock = selected.data('stock') == undefined ? "" : selected.data('stock');
        // $('#qty').val(selected.data('stock'));
        $('.stok_warehouse').val(stock + ' ' + satuan_name);
    });

    $('#operasi').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true
    });

    // VALIDATOR HEADER
    var validator = $(".create-form").validate({
        rules: {
            divisi_id: {
                required: true
            },
            type_pengambilan_stock: {
                required: true
            },
        },
        messages: {
            divisi_id: {
                required: "Pilih departemen"
            },
            type_pengambilan_stock: {
                required: "Pilih tipe pengambilan stok"
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


    $("#type_barang,#divisi_id,#warehouse_id,#spesifikasi_id,#bc_id,#no_aju,#operasi_fifo,#tipe_adjusment,#type_pengambilan_stock")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    // SUBMIT HEADER
    $('.btn-submit-parent').click(function() {
        if (listStock.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang masih kosong',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            if ($('.create-form').valid()) {
                var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
                var isValidTipeAdjusment = true;
                var dataErrorTipeAdjusment = null;

                var isValidQtyAdjusment = true;
                var dataErrorQtyAdjusment = null;

                if (typePengambilanStock == "PABEAN") {
                    $.each(listStock, function(i, v) {
                        var elementTypeAdjusment = $('select[data-id="' + v.id + '"].type_adjusment');
                        var elementQtyAdjusment = $('input[data-id="' + v.id + '"].qty_adjusment');

                        if (elementTypeAdjusment.val() == "") {
                            dataErrorTipeAdjusment = listStock[i];
                            isValidTipeAdjusment = false;
                        } else {
                            listStock[i].type_adjusment = elementTypeAdjusment.val();
                        }

                        if (elementQtyAdjusment.val() == "" || parseFloat(elementQtyAdjusment.val()) == 0) {
                            dataErrorQtyAdjusment = listStock[i];
                            isValidQtyAdjusment = false;
                        } else {
                            listStock[i].qty_adjusment = parseFloat(elementQtyAdjusment.val());
                        }

                        if (elementTypeAdjusment.val() == "MINUS") {
                            if (parseFloat(elementQtyAdjusment.val()) > parseFloat(elementQtyAdjusment.data('stock_total'))) {
                                dataErrorQtyAdjusment = listStock[i];
                                isValidQtyAdjusment = false;
                            } else {
                                listStock[i].qty_adjusment = parseFloat(elementQtyAdjusment.val());
                            }
                        }
                    });
                }

                if (!isValidTipeAdjusment) {
                    Swal.fire({
                        icon: 'error',
                        title: "Tipe Adjusment Barang " + dataErrorTipeAdjusment.barang + ', dengan nomor pabean ' + dataErrorTipeAdjusment.dokumen_text + ' belum dipilih',
                        confirmButtonColor: '#4e73df',
                    })
                } else if (!isValidQtyAdjusment) {
                    Swal.fire({
                        icon: 'error',
                        title: "Qty Adjusment Barang " + dataErrorQtyAdjusment.barang + ', dengan nomor pabean ' + dataErrorQtyAdjusment.dokumen_text + ' tidak valid',
                        confirmButtonColor: '#4e73df',
                    })
                } else {
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
                            var id = $('.id').val();
                            var formData = new FormData(document.querySelector('.create-form'));
                            formData.append("no_adjusment", $('#no_adjusment').val());
                            formData.append("listBarang", JSON.stringify(listStock));
                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("stock-adjusment/update"); ?>",
                                    data: formData,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
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
                                                    location.reload();
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        }
                                    },

                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("stock-adjusment/save"); ?>",
                                    data: formData,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
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
                                                    location.replace('<?= base_url("stock-adjusment/id") ?>/' + response.id, "_blank")
                                                })
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        }
                                    },

                                });
                            }
                        }
                    })
                }


            }
        }
    })

    // PILIH ADJUSMENT
    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
        console.log(listStock);
    });

    $('.posting-adjusment').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("stock-adjusment/posting"); ?>",
                    data: {
                        id: $('.id').val()
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
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            })
                        }
                    },
                });
            }
        })
    })

    $('.delete-parent').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Adjusment ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("stock-adjusment/delete"); ?>",
                    data: {
                        id: $('.id').val()
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
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                confirmButtonColor: '#4e73df',
                            }).then((result) => {
                                window.location.href = "<?= base_url('stock-adjusment') ?>"
                            });
                        }
                    },
                });
            }
        })
    });

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStock, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockInventori, function(i, v) {
            var currentID = Number(v.id);
            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listStock, function(item) {
                    return item.id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listStockInventori[i].type_adjusment = "";
                    listStockInventori[i].qty_adjusment = 0;
                    listStockInventori[i].kode_barang = $('#spesifikasi_id option:selected').data('kode_barang');
                    listStockInventori[i].warehouse_text = $('#warehouse_id option:selected').text();
                    listStockInventori[i].warehouse_id = $('#warehouse_id option:selected').val();
                    listStockInventori[i].dokumen_text = listStockInventori[i].bc_type + " / " + listStockInventori[i].no_aju;
                    listStock.push(listStockInventori[i]);
                }
            }
        });

        drawTable(listStock);
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var operasiFifo = $('#operasi_fifo option:selected').val();
        var qtyAdjusmentFifo = parseFloat($('#qty_adjusment_fifo').val());
        var stockID = $(".spesifikasi_id option:selected").data('stock_id');
        if (operasiFifo == "" || isNaN(qtyAdjusmentFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Operasi dan Qty Adjusment Fifo Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            var totalStokTotal = 0;
            $.each(listStockInventori, function(i, v) {
                totalStokTotal += parseFloat(v.stok_total);
            });

            deleteByStockID(stockID);

            if (operasiFifo == "MINUS") {
                if (qtyAdjusmentFifo > totalStokTotal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan : Operasi FIFO akan menghasilkan nilai minus',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        reverseButtons: true,
                        confirmButtonText: 'Oke',
                    })
                } else {
                    $.each(listStockInventori, function(i, v) {
                        var currentID = Number(v.id);
                        if ($.inArray(currentID, dataIds) == -1) {
                            var isIDSelected = $.grep(listStock, function(item) {
                                return item.id == Number(currentID);
                            }).length > 0;
                            if (!isIDSelected && qtyAdjusmentFifo != 0 && parseFloat(listStockInventori[i].stok_total) != 0) {
                                var adjusmentQty = Math.min(qtyAdjusmentFifo, parseFloat(listStockInventori[i].stok_total));

                                listStockInventori[i].type_adjusment = "MINUS";
                                listStockInventori[i].qty_adjusment = adjusmentQty.toFixed(2);
                                listStockInventori[i].kode_barang = $('#spesifikasi_id option:selected').data('kode_barang');
                                listStockInventori[i].warehouse_text = $('#warehouse_id option:selected').text();
                                listStockInventori[i].warehouse_id = $('#warehouse_id option:selected').val();
                                listStockInventori[i].dokumen_text = listStockInventori[i].bc_type + " / " + listStockInventori[i].no_aju;
                                listStock.push(listStockInventori[i]);

                                qtyAdjusmentFifo = qtyAdjusmentFifo - adjusmentQty;
                            }
                        }
                    });
                }
            } else {
                $.each(listStockInventori, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStock, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;

                        if (!isIDSelected && qtyAdjusmentFifo != 0 && parseFloat(listStockInventori[i].stok_total) != 0) {
                            var adjusmentQty = Math.min(qtyAdjusmentFifo, parseFloat(listStockInventori[i].stok_total));

                            listStockInventori[i].type_adjusment = "PLUS";
                            listStockInventori[i].qty_adjusment = adjusmentQty.toFixed(2);
                            listStockInventori[i].kode_barang = $('#spesifikasi_id option:selected').data('kode_barang');
                            listStockInventori[i].warehouse_text = $('#warehouse_id option:selected').text();
                            listStockInventori[i].warehouse_id = $('#warehouse_id option:selected').val();
                            listStockInventori[i].dokumen_text = listStockInventori[i].bc_type + " / " + listStockInventori[i].no_aju;
                            listStock.push(listStockInventori[i]);

                            qtyAdjusmentFifo = qtyAdjusmentFifo - adjusmentQty;

                        }
                    }
                });
            }
            drawTable();
        }
    }

    function deleteByStockID(stockID) {
        listStock = listStock.filter(function(item) {
            return item.stock_id != stockID;
        });
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStock.splice(indexToRemove, 1);
            drawTable();
        }
    }

    function drawTable() {
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        const table = $('.table-adjusment');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listStock.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="14" style="text-align: center;">').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listStock, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.type_barang_text));
                newRow.append($('<td>').text(v.sumber));
                newRow.append($('<td>').text(v.stock_dokumen));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang));
                newRow.append($('<td>').text(v.satuan));
                newRow.append($('<td>').text(v.warehouse_text));
                newRow.append($('<td>').text(v.dokumen_text));
                newRow.append($('<td>').text(v.stok_total));
                if (typePengambilanStok == "FIFO") {
                    newRow.append($('<td>').text(
                        `${v.type_adjusment == "PLUS" ? "( + )" : "( - )"}`
                    ));
                    newRow.append($('<td>').text(
                        v.qty_adjusment
                    ));
                } else {
                    newRow.append($('<td>').html(
                        `
                        <div class="row justify-content-center row-col-spp">
                            <div class="col">
                                <select <?= !empty($adjusment) ? ($adjusment['status_posting'] == "1" ? 'disabled' : '') : '' ?> class="form-select type_adjusment" id="type_adjusment" name="type_adjusment" data-id="${v.id}" aria-label="Floating label select example">
                                    <option value="">OPERASI</option>
                                    <option ${v.type_adjusment == "PLUS" ? 'selected' : ''} value="PLUS">( + )</option>
                                    <option ${v.type_adjusment == "MINUS" ? 'selected' : ''} value="MINUS">( - )</option>
                                </select>
                            </div>
                        </div>
                    `
                    ));

                    newRow.append($('<td>').html(
                        `
                        <input <?= !empty($adjusment) ? (($adjusment['status_posting'] == "1") ? 'disabled' : '') : '' ?> style="height: 40px; padding-bottom: 10px;" class="form-control qty_adjusment" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-stock_total="${v.stok_total}" data-id="${v.id}" class="form-control" type="text" value="${v.qty_adjusment}">
                    `

                    ));
                }


                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($adjusment) ? (($adjusment['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });
        }

    }

    function drawTableListInventori() {
        if ($.fn.DataTable.isDataTable('.table-inventori')) {
            $('.table-inventori').DataTable().clear().draw();
            dataTableListStock.destroy();
        }
        const table = $('.table-inventori');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();

        $.each(listStockInventori, function(i, v) {
            var newRow = $('<tr style="color:whitesmoke;">');
            if (typePengambilanStok == "FIFO") {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                `
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input  data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));
            }

            newRow.append($('<td>').text(v.sumber));
            newRow.append($('<td>').text(v.stock_dokumen));
            newRow.append($('<td>').text(v.supplier_name));
            newRow.append($('<td>').text(v.bc_type));
            newRow.append($('<td>').text(v.no_aju));
            newRow.append($('<td>').text(v.stock_date));
            newRow.append($('<td>').text(v.barang));
            newRow.append($('<td>').text(v.satuan));
            newRow.append($('<td>').text(v.stok_total));

            table.find('tbody').append(newRow);
        });

        dataTableListStock = $('.table-inventori').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: false,
            serverSide: false,
            ordering: true,
            order: [],
            fixedHeader: true,
            "initComplete": function(settings, json) {
                $('.dataTables_length').empty();
                $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
            },
            display: "stripe",
            searching: true,
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        dataTableListStock.draw();
    }

    function preventNegativeInput(inputElement) {
        var inputValue = inputElement.value;
        var numericValue = inputValue.replace(/[^0-9.]/g, '');
        numericValue = numericValue.replace(/^0+/g, '');
        numericValue = numericValue.replace(/^\./g, '0.');
        if (parseFloat(numericValue) < 0 || isNaN(parseFloat(numericValue))) {
            inputElement.value = '0';
        } else {
            inputElement.value = numericValue;
        }
    }

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('stock-adjusment/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
            },
            dataType: "json",
            success: function(res) {
                // DRAW TABLE LIST INVENTORI BARANG
                listStockInventori = res.data
                drawTableListInventori();
            }
        });
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
                    $(".spesifikasi_id").append(`<option data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_adjusment").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("stock-adjusment/get-adjusment-no"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_adjusment").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_adjusment").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_adjusment").val("");
                    }
                }
            })
        } else {
            $(".no_adjusment").attr("readonly", false);
            $(".no_adjusment").val("");
        }
    }
</script>



<?= $this->endSection(); ?>