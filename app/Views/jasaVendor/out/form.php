<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($jasaVendorOut) ? "Tambah Jasa Vendor Barang Keluar" : "Update Jasa Vendor Barang Keluar" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("jasa-vendor-out"); ?>">
                Kembali
            </a>
            <?php if (!empty($jasaVendorOut)) : ?>
                <?php if ($jasaVendorOut['status_posting'] == "0") : ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($jasaVendorOut['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($jasaVendorOut['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-out/print/"); ?><?= encrypt($jasaVendorOut['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Jasa Vendor', 'Barang Keluar', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("jasa-vendor-out/print/"); ?><?= encrypt($jasaVendorOut['id']); ?>')">
                            Print
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
                    <label class="form-label font-weight-bold lable-title">Data Pengeluaran Barang</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($jasaVendorOut) ? encrypt($jasaVendorOut['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'readonly' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($jasaVendorOut) ? $jasaVendorOut['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Dibuat</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($jasaVendorOut) ? 'disabled=true' : ''; ?> value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_surat_jalan'] : "TOBA-VBK//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Surat Jalan">
                                    <label for="floatingInput">No. Nota Surat Jalan</label>
                                </div>
                                <div style="<?= !empty($jasaVendorOut) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Nomor Kontainer" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_kontainer'] : '' ?>" class="form-control no_kontainer" id="no_kontainer" name="no_kontainer" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Nomor Kontainer (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($warehouse)) : ?>
                                    <?php foreach ($warehouse as $w) : ?>
                                        <option <?= $jasaVendorOut['warehouse_id'] == $w['id'] ? 'selected' : '' ?> value="<?= $w['id'] ?>">
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
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-layout">
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
                                <select class="form-select type_barang" disabled id="type_barang" name="type_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($tipeBarang as $t) : ?>
                                        <?php if ($t['description'] == "bahan_baku") : ?>
                                            <option selected value="<?= $t['description'] ?>">
                                                <?= strtoupper($t['value']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id" aria-label="Floating label select example">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Udang / Kepiting (Kirim Ke Vendor)</label>
                            </div>
                        </div>

                        <div class="col-md-4 form-fifo">
                            <div class="form-floating" style="height: 50px;">
                                <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty_keluar_fifo" id="qty_keluar_fifo" name="qty_keluar_fifo" aria-label="Floating label select example" />
                                <label for="floatingInput" style="z-index: 1;">Qty Dikeluarkan</label>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="row mt-3">
                    <div class="col mb-0">
                        <label class="form-label font-weight-bold lable-title">List Inventori Barang (Hanya Menampilkan Barang yang mempunyai Stok)</label>
                    </div>
                    <div class="col-md-12 col-table-button-tts">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts" id="dataTable" width="100%" cellspacing="0">
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
                                <tbody class="body-table">
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary" id="select-item-btn">Pilih</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col mb-3">
                    <label class="form-label font-weight-bold lable-title">Daftar Barang Yang Akan Dikeluarkan</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">Tanggal Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Satuan</th>
                                    <th style="text-align: center;">Sisa Qty</th>
                                    <th style="text-align: center;">Qty Dikeluarkan</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="11" style="text-align: center;">
                                        Tidak Ada Barang
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listStockAsal = [];
    var listStockSelected = [];

    var dataTable = $('#dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: false,
        serverSide: false,
        ordering: false,
        order: [],
        fixedHeader: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        lengthMenu: [
            [100],
            [100]
        ],
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

    <?php if (!empty($jasaVendorOut)) : ?>
        <?php if ($jasaVendorOut['tipe_pengambilan_stock'] == "FIFO") : ?>
            $('.form-fifo').show();
        <?php else : ?>
            $('.form-fifo').hide();
        <?php endif; ?>
    <?php else : ?>
        $('.form-fifo').hide();
    <?php endif; ?>

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
        // RESET
        $('#spesifikasi_id').val(null).change();
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
    });

    <?php if (!empty($jasaVendorOut)) : ?>
        // GET LIST BARANG 
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/list-barang-stock-init'); ?>`,
            method: "GET",
            beforeSend: function() {},
            complete: function() {},
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
        // APPEND 
        <?php foreach ($jasaVendorOutDetail as $m) : ?>
            listStockSelected.push({
                id: "<?= $m['id'] ?>",
                bc_id: "<?= $m['bc_id'] ?>",
                stock_dokumen: "<?= $m['stock_dokumen'] ?>",
                sumber: "<?= $m['sumber'] ?>",
                supplier_name: "<?= $m['supplier_name'] ?>",
                stock_detail_id: "<?= $m['stock_detail_id'] ?>",
                no_aju: "<?= $m['no_aju'] ?>",
                stock_id: "<?= $m['stock_id'] ?>",
                stok_total: "<?= $m['stok_total'] ?>",
                bc_type: "<?= $m['bc_type'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                barang: "<?= $m['barang'] ?>",
                type_barang: "<?= $m['type_barang'] ?>",
                type_barang_text: "<?= $m['type_barang_text'] ?>",
                stock_date: "<?= $m['stock_date'] ?>",
                qty: "<?= $m['qty'] ?>",
            });
        <?php endforeach; ?>
        drawTableSelectedItem(listStockSelected);
        <?php if ($jasaVendorOut['status_posting']) : ?>
            $('.detail-form-layout').hide()
        <?php endif; ?>
    <?php endif; ?>


    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('#vendor_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
        // GET NO SURAT JALAN
        changeStatus();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        getListWarehouse();
        listStockAsal = [];
        listStockSelected = [];
        drawTableAsalBarang(listStockAsal);
        drawTableSelectedItem(listStockSelected);
        // GET BARANG
        getListBarang();
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        // GET LIST BARANG
        getListBarang();
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Udang / Kepiting (Kirim Ke Vendor)",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabean();
    });

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            no_surat_jalan: {
                required: true
            },
            vendor_id: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
        },
        messages: {
            no_surat_jalan: {
                required: "No surat jalan wajib diisi"
            },
            vendor_id: {
                required: "Vendor wajib diisi"
            },
            keterangan: {
                required: "Keterangan wajib diisi"
            },
            divisi_id: {
                required: "Pilih Departemen"
            },
            warehouse_id: {
                required: "Pilih Warehouse"
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


    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
    });

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();
        var id_selected = getIDListDataSelected();

        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id);
            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listStockSelected, function(item) {
                    return item.id == Number(currentID);
                }).length > 0;

                if (!isIDSelected) {
                    listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                    listStockAsal[i].qty = 0;
                    listStockSelected.push(listStockAsal[i]);
                }
            }
        });
        drawTableSelectedItem(listStockSelected);
        // reset barang kirim ke vendor dan list bc nya
        $('#spesifikasi_id').val(null).change();
        drawTableAsalBarang([]);
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var qtyKeluarFifo = parseFloat($('#qty_keluar_fifo').val());
        var stockOutID = $(".spesifikasi_id option:selected").data('stock_id');

        if (isNaN(qtyKeluarFifo) || qtyKeluarFifo == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Keluar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            })
        } else {
            var totalStokTotal = 0;
            $.each(listStockAsal, function(i, v) {
                totalStokTotal += parseFloat(v.stok_total);
            });

            if (qtyKeluarFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang yang akan keluar tidak cukup !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                })
            } else {
                deleteByStockID(stockOutID);
                // STOK APPEND
                $.each(listStockAsal, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStockSelected, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;
                        if (!isIDSelected && qtyKeluarFifo != 0 && parseFloat(listStockAsal[i].stok_total) != 0) {
                            var keluarQty = Math.min(qtyKeluarFifo, parseFloat(listStockAsal[i].stok_total));
                            listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                            listStockAsal[i].qty = parseFloat(keluarQty.toFixed(4));

                            listStockSelected.push(listStockAsal[i]);
                            qtyKeluarFifo = qtyKeluarFifo - keluarQty;
                        }
                    }
                });
                drawTableSelectedItem(listStockSelected);
            }

        }

    }

    function deleteByStockID(stockID) {
        listStockSelected = listStockSelected.filter(function(item) {
            return item.stock_id != stockID;
        });
    }


    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dikirimkan ke vendor tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
            if ($('.create-form').valid()) {
                var isValid = true;
                var dataError = null;

                if (typePengambilanStock == "PABEAN") {
                    $.each(listStockSelected, function(i, v) {
                        var element = $('input[data-id="' + v.id + '"].stok-out');
                        var input_user = parseFloat(element.val());
                        var stok_max = parseFloat(element.data('stok_total'));

                        if (input_user > stok_max || isNaN(input_user) || input_user == undefined || input_user == 0) {
                            dataError = listStockSelected[i];
                            isValid = false;
                        } else {
                            listStockSelected[i].qty = input_user;
                        }
                    });
                }


                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok keluar, barang ' + dataError.barang + ' dengan dokumen ' + dataError.bc_type + ' / ' + dataError.no_aju + ' tidak valid!',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan Data ?',
                        confirmButtonColor: '#4e73df',
                        cancelButtonColor: '#d33',
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Kembali',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let id = $('#id').val();
                            let data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listStockSelected));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-out/update"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                            confirmButtonText: 'Ok'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "<?= base_url("jasa-vendor-out") ?>";
                                            }
                                        });
                                    },
                                });
                            } else {
                                // INSERT
                                $.ajax({
                                    url: "<?= base_url("jasa-vendor-out/save"); ?>",
                                    data: data,
                                    beforeSend: function(xhr) {
                                        xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                                        setLoading();
                                    },
                                    complete: function() {
                                        stopLoading()
                                    },
                                    method: "POST",
                                    dataType: "json",
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        if (response.status == false) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: res.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                        } else {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("jasa-vendor-out") ?>";
                                                }
                                            });
                                        }

                                    },
                                });
                            }
                        }
                    });
                }

            }
        }
    });


    $("#vendor_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_id,#type_pengambilan_stock")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function getListWarehouse() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/warehouse'); ?>`,
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
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/list-barang-stock-init'); ?>`,
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

    function getListDokumenPabean() {
        // GET LIST STOCK PER DOKUMEN PABEAN
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/list-stock-dokumen-bc'); ?>`,
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
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                drawTableAsalBarang(res.data);
            }
        });
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();

        $.each(data, function(i, v) {
            var newRow = $('<tr>');
            if (typePengambilanStok == "FIFO" || parseFloat(v.stok_total) == 0) {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                `
                ));
            } else {
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
                ));
            }

            newRow.append($('<td style="text-align:center;">').text(v.sumber));
            newRow.append($('<td style="text-align:center;">').text(v.stock_dokumen));
            newRow.append($('<td style="text-align:center;">').text(v.supplier_name));
            newRow.append($('<td style="text-align:center;">').text(v.bc_type));
            newRow.append($('<td style="text-align:center;">').text(v.no_aju));
            newRow.append($('<td style="text-align:center;">').text(v.stock_date));
            newRow.append($('<td style="text-align:center;">').text(v.barang));
            newRow.append($('<td style="text-align:center;">').text(v.satuan));
            newRow.append($('<td style="text-align:center;">').text(v.stok_total));
            table.find('tbody').append(newRow);
        });

        dataTable = $('#dataTable').DataTable({
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
            lengthMenu: [
                [100],
                [100]
            ],
            language: {
                emptyTable: "Tidak Ada Data",
                lengthMenu: "Show _MENU_ entries",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        dataTable.draw();
    }

    function drawTableSelectedItem(data) {
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        const table = $('#selectedItemTable');
        var no = 1;
        $('.foot-detail-table').empty();
        $('.body-table').empty();

        if (data.length == 0) {
            var newRow = '';
            newRow += `
                    <tr>
                        <td colspan="11" style="text-align: center;">
                            Tidak Ada Barang
                        </td>
                    </tr>
                `;
            $('.foot-detail-table').append(newRow);
        } else {
            var totalQtyKeluar = 0;
            $.each(data, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td style="text-align: center;">').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td style="text-align: center;">').text(v.sumber));
                newRow.append($('<td style="text-align: center;">').text(v.stock_dokumen));
                newRow.append($('<td style="text-align: center;">').text(v.supplier_name));
                newRow.append($('<td style="text-align: center;">').text(v.bc_type + '/' + v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));
                newRow.append($('<td style="text-align: center;">').text(v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.satuan));
                newRow.append($('<td style="text-align: center;">').text(v.stok_total));
                if (typePengambilanStok == "FIFO") {
                    newRow.append($('<td style="text-align: center;">').text(v.qty));
                } else {
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                    <input <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> class="form-control stok-out" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${v.qty}">
                `
                    ));
                }

                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);

                totalQtyKeluar += Number(v.qty);
            });

            var newRow = $('<tr style="color:whitesmoke; background-color:#f2c996;">');
            newRow.append($('<td style="text-align: right;" colspan="9">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td>').text(totalQtyKeluar.toFixed(3)));
            newRow.append($('<td>').text(''));
            table.find('tbody').append(newRow);


        }



    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (var i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                indexToRemove = i;
                break;
            }
        }
        if (indexToRemove !== -1) {
            listStockSelected.splice(indexToRemove, 1);
            drawTableSelectedItem(listStockSelected);
        }
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_surat_jalan").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("jasa-vendor-out/get-jasa-vendor-out-no"); ?>`,
                method: "GET",
                data: {
                    warehouse_id: $('#warehouse_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_surat_jalan").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_surat_jalan").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_surat_jalan").val("");
                    }
                }
            })
        } else {
            $(".no_surat_jalan").attr("readonly", false);
            $(".no_surat_jalan").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Jasa Vendor Barang Keluar ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-out/posting"); ?>",
                    data: {
                        id: id
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
                                window.location.href = "<?= base_url("jasa-vendor-out") ?>";
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
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Jasa Vendor Barang Keluar ?',
            confirmButtonColor: '#4e73df',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Kembali',
        }).then((result) => {
            if (result.isConfirmed) {
                const csrf = $(`[name="${csrfToken}"]`);
                $.ajax({
                    url: "<?= base_url("jasa-vendor-out/delete"); ?>",
                    data: {
                        id: id
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
                                window.location.href = "<?= base_url("jasa-vendor-out") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>