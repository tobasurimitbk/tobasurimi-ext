<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<style>
    /* Pastikan container form-floating tidak mengubah posisi */
    .form-floating > .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 100% !important;
        height: 50px !important; /* Tinggi fix */
        padding: 4px 6px !important;
        display: block !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        overflow-y: auto;   /* Scroll vertikal */
        overflow-x: hidden;
        white-space: normal;
    }

    /* Chip/tag pilihan */
    .select2-container--bootstrap-5 .select2-selection__choice {
        background-color: #e9ecef !important;
        border: none !important;
        padding: 2px 6px !important;
        margin: 2px 4px 0 0 !important;
        font-size: 0.8rem !important;
        border-radius: 0.25rem !important;
        display: inline-flex;
        align-items: center;
    }

    /* Ikon X di chip */
    .select2-container--bootstrap-5 .select2-selection__choice__remove {
        margin-right: 4px !important;
    }

    /* Search box di dalam multiple select */
    .select2-container--bootstrap-5 .select2-search--inline {
        display: inline-flex;
        align-items: center;
    }
    .select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
        margin-top: 0 !important;
        padding: 0 !important;
        height: auto !important;
    }
    
</style>

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
                                    <input readonly autocomplete="one-time-code" <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'readonly' : '') : ''; ?> value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_surat_jalan'] : "TOBA-VBK//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_surat_jalan" id="no_surat_jalan" name="no_surat_jalan" placeholder="No. Surat Jalan">
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
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select vendor_id" id="vendor_id" name="vendor_id">
                                <option value=""></option>
                                <?php foreach ($vendor as $v) : ?>
                                    <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['vendor_id'] == $v['id'] ? 'selected' : '') : '' ?> value="<?= $v['id'] ?>">
                                        <?= strtoupper($v['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Vendor Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Nomor Kontainer" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['no_kontainer'] : '' ?>" class="form-control no_kontainer" id="no_kontainer" name="no_kontainer" />
                            <label for="floatingInput" style="z-index: 1;">Nomor Kontainer (Opsional)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id">
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
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
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
                            <select <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock">
                                <option value=""></option>
                                <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['tipe_pengambilan_stock'] == "PABEAN" ? 'selected' : '') : '' ?> value="PABEAN">PABEAN</option>
                                <option <?= !empty($jasaVendorOut) ? ($jasaVendorOut['tipe_pengambilan_stock'] == "FIFO" ? 'selected' : '') : '' ?> value="FIFO">FIFO</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($jasaVendorOut) ? ($jasaVendorOut['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($jasaVendorOut) ? $jasaVendorOut['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-layout">

                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Data Barang Dari Pembelian / Vendor</label>
                    </div>
                </div>
                <form class="detail-form">
                    <input type="hidden" name="id_detail" class="id_detail" id="id_detail">
                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" id="type_barang" name="type_barang">
                                    <option value=""></option>
                                    <option value="BAHAN_BAKU" selected>BAHAN BAKU</option>
                                    <option value="BAHAN_SETENGAH_JADI">BAHAN SETENGAH JADI</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_asal_barang" id="type_asal_barang" name="type_asal_barang">
                                    <option value=""></option>
                                    <option value="SUPPLIER" selected>SUPPLIER</option>
                                    <option value="VENDOR">VENDOR</option>
                                    <option value="PRODUKSI">PRODUKSI</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Asal Barang</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="supplier_id_select">
                            <div class="form-floating mb-3" id="supplier_id_select" style="height: 50px;">
                                <select class="form-select supplier_id" id="supplier_id" name="supplier_id[]" multiple>
                                    <?php foreach ($supplier as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="supplier_id">Pilih Supplier (Bisa multiple)</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="vendor_barang_id_select">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select vendor_barang_id" id="vendor_barang_id" name="vendor_barang_id">
                                    <option value=""></option>
                                    <?php foreach ($vendor as $v): ?>
                                        <option value="<?= $v['id'] ?>"> <?= strtoupper($v['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Vendor</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id">
                                    <option value=""></option>

                                </select>
                                <label for="floatingInput" style="z-index: 1;">Udang / Kepiting (Kirim Ke Vendor)</label>
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
                                        <th style="text-align: center;">No PO</th>
                                        <th style="text-align: center;">Supplier / Vendor</th>
                                        <th style="text-align: center;">Dokumen Pabean</th>
                                        <!-- <th style="text-align: center;">No Aju / No Daftar</th> -->
                                        <th style="text-align: center;">Tgl PO / Tgl Vendor Masuk</th>
                                        <th style="text-align: center;">Barang - Spesifikasi</th>
                                        <th style="text-align: center;">Satuan</th>
                                        <th style="text-align: center;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="8" style="text-align:right">Total:</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row">
                                <div class="col-md-4 mb-3 form-fifo">
                                    <div class="form-floating" style="height: 50px;">
                                        <input placeholder="Qty" oninput="preventNegativeInput(this)" class="form-control qty_keluar_fifo" id="qty_keluar_fifo" name="qty_keluar_fifo" />
                                        <label for="floatingInput" style="z-index: 1;">Qty Dikeluarkan</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-floating" style="height: 50px;">
                                        <input placeholder="keterangan" class="form-control keterangan_detail" id="keterangan_detail" name="keterangan_detail" />
                                        <label for="floatingInput" style="z-index: 1;">Keterangan</label>
                                    </div>
                                </div>
                            </div>
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
                                    <th style="text-align: center;">No PO</th>
                                    <th style="text-align: center;">Supplier / Vendor</th>
                                    <th style="text-align: center;">Keterangan</th>
                                    <!-- <th style="text-align: center;">Dokumen Pabean</th> -->
                                    <th style="text-align: center;">Tgl PO / Tgl Vendor Masuk</th>
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

    <?php if (empty($jasaVendorOut)): ?>
        $('.tanggal').change(function() {
            changeStatus();
        })
    <?php endif; ?>

    $('#vendor_barang_id_select').hide();

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
                warehouse_id: $(".warehouse_id option:selected").val(),
                asal_barang: $(".type_asal_barang option:selected").val()
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
                reference_type: "<?= $m['sumber'] ?>",
                supplier_name: "<?= $m['supplier_name'] ?>",
                stock_detail_id: "<?= $m['stock_detail_id'] ?>",
                no_aju: "<?= $m['no_aju'] ?>",
                stock_id: "<?= $m['stock_id'] ?>",
                po_id: "<?= $m['po_id'] ?>",
                stok_total: "<?= floatval($m['stok_total']) ?>",
                bc_type: "<?= $m['bc_type'] ?>",
                satuan: "<?= $m['satuan'] ?>",
                satuan_id: "<?= $m['satuan_id'] ?>",
                barang: "<?= $m['barang'] ?>",
                type_barang: "<?= $m['type_barang'] ?>",
                type_barang_text: "<?= $m['type_barang_text'] ?>",
                keterangan: "<?= $m['keterangan'] ?>",
                stock_date: "<?= $m['stock_date'] ?>",
                qty: "<?= floatval($m['qty']) ?>",
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
        placeholder: "Pilih Vendor Tujuan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {});

    $('#type_asal_barang').select2({
        placeholder: "Pilih Asal Barang",
        theme: "bootstrap-5",
    }).change(function() {
        getDropdownAsalBarang();
        // listStockAsal = [];
        // drawTableAsalBarang(listStockAsal);
        // getListDokumenPabean();
    });

    $('#vendor_barang_id').select2({
        placeholder: "Pilih Vendor",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // LIST DOKUMEN PABEAN
        getListDokumenPabean();
    });

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
        allowClear: true,
    }).change(function() {
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
        getListDokumenPabean();
    });

    $('#supplier_id').select2({
        placeholder: "Pilih Supplier",
        theme: "bootstrap-5",
        multiple: true,
        // allowClear: true,
        width: '100%',
        dropdownParent: $('#supplier_id_select')
    }).change(function() {
        // GET LIST BARANG
        // getListBarang();
        listStockAsal = [];
        drawTableAsalBarang(listStockAsal);
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
            type_pengambilan_stock: {
                required: true

            }
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
            type_pengambilan_stock: {
                required: "Pilih Tip[e Pengambilan Stock"
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

   // Ubah fungsi insertListPabean menjadi:
    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var keteranganDetail = $('#keterangan_detail').val();
        var dataIds = checkedCheckboxes.map(function() {
            return Number($(this).data("id")); // Konversi ke number
        }).get();
        
        var id_selected = getIDListDataSelected();
        
        $.each(listStockAsal, function(i, v) {
            var currentID = Number(v.id); // Konversi ke number
            if ($.inArray(currentID, dataIds) !== -1) {
                var isIDSelected = $.grep(listStockSelected, function(item) {
                    return Number(item.id) == currentID; // Konversi ke number
                }).length > 0;
                
                if (!isIDSelected) {
                    listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                    listStockAsal[i].keterangan = keteranganDetail;
                    listStockAsal[i].qty = 0; // Ganti dari 0 ke 1 atau nilai default lain
                    listStockSelected.push(listStockAsal[i]);
                }
            }
        });
        drawTableSelectedItem(listStockSelected);
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var keteranganDetail = $('#keterangan_detail').val();
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
            });
            return;
        }

         // Calculate total available stock (excluding already selected items)
        var totalAvailableStock = listStockAsal.reduce((total, item) => {
            const isSelected = dataIds.includes(Number(item.id)) || 
                listStockSelected.some(selectedItem => selectedItem.id === Number(item.id));
            return isSelected ? total : total + parseFloat(item.stok_total || 0);
        }, 0);

        // Stock availability validation
        if (qtyKeluarFifo > totalAvailableStock) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Tidak Cukup',
                text: `Stok tersedia: ${totalAvailableStock.toFixed(4)} (Permintaan: ${qtyKeluarFifo.toFixed(4)})`,
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        }

        // Delete existing items with the same stock ID
        deleteByStockID(stockOutID);

        // Sort by date (FIFO) if needed
        // listStockAsal.sort((a, b) => new Date(a.stock_date) - new Date(b.stock_date));

        // First, try to find exact matches where stok_total equals qtyKeluarFifo
        var exactMatch = listStockAsal.find(item => 
            parseFloat(item.stok_total) === qtyKeluarFifo && 
            !dataIds.includes(Number(item.id)) &&
            $.grep(listStockSelected, selectedItem => selectedItem.id == Number(item.id)).length === 0);

        if (exactMatch) {
            exactMatch.qty = parseFloat(exactMatch.stok_total);
            exactMatch.keterangan = keteranganDetail; // ⬅️ masukin di sini
            listStockSelected.push(exactMatch);
            qtyKeluarFifo = 0;
        } else {
            // If no exact match, find items with sufficient quantity
            var sufficientItem = listStockAsal.find(item => 
                parseFloat(item.stok_total) >= qtyKeluarFifo && 
                !dataIds.includes(Number(item.id)) &&
                $.grep(listStockSelected, selectedItem => selectedItem.id == Number(item.id)).length === 0);

            if (sufficientItem) {
                sufficientItem.qty = qtyKeluarFifo;
                sufficientItem.keterangan = keteranganDetail;
                listStockSelected.push(sufficientItem);
                qtyKeluarFifo = 0;
            } else {
                // If no single item has enough, take the largest available first
                 listStockAsal.sort((a, b) => new Date(a.stock_date) - new Date(b.stock_date));

                // Process items in FIFO order
                for (let i = 0; i < listStockAsal.length && qtyKeluarFifo > 0; i++) {
                    const item = listStockAsal[i];
                    const isSelected = dataIds.includes(Number(item.id)) || 
                        $.grep(listStockSelected, selectedItem => selectedItem.id == Number(item.id)).length > 0;
                    
                    if (!isSelected && parseFloat(item.stok_total) > 0) {
                        const availableQty = parseFloat(item.stok_total);
                        const takenQty = Math.min(availableQty, qtyKeluarFifo);
                        
                        const newItem = {...item}; // Create a copy
                        newItem.qty = parseFloat(takenQty.toFixed(4));
                        newItem.keterangan = keteranganDetail;
                        listStockSelected.push(newItem);
                        
                        qtyKeluarFifo -= takenQty;
                    }
                }
            }
        }

        drawTableSelectedItem(listStockSelected);
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
                        const $el = $('input[data-id="' + v.id + '"].stok-out');
                        const input_user = destroyFormatRupiah($el.val()); // <= INI KUNCI
                        const stok_max = Number($el.data('stok_total')) || 0;
                        listStockSelected[i].qty = input_user;
                    });
                }


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

                            // Ambil semua field dari .detail-form
                            document.querySelectorAll(".detail-form [name]").forEach(el => {
                                data.append(el.name, el.value);
                            });

                            // Append list barang
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
                                                title: response.message,
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
    });


    $("#vendor_id,#warehouse_id,#divisi_id,#type_barang,#spesifikasi_id,#type_pengambilan_stock,#supplier_id,#type_asal_barang,#vendor_barang_id")
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
                warehouse_id: $(".warehouse_id option:selected").val(),
                asal_barang: $(".type_asal_barang option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                $(".spesifikasi_id").empty()
                $(".spesifikasi_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".spesifikasi_id").append(`<option data-barang_master_id="${item.id}" data-stock_id="${item.stock_id}" data-kode_barang="${item.kode_barang}" data-barang="${item.barang}" data-kode_satuan="${item.kode_satuan}" value="${item.spesifikasi_id}">(${item.kode_barang}) ${item.barang}</option>`)
                })
                $(".spesifikasi_id").val();
            }
        });
    }

    


    // helper: parse stok_total aman
    function parseFloatSafe(val) {
        if (val === null || val === undefined || val === '') return 0;
        return parseFloat(String(val).replace(/,/g, '.')) || 0;
    }

    // helper: buat key unik per record (ubah fields sesuai kebutuhan)
    function makeUniqueKey(v) {
        // include stock_dokumen & bc_id supaya satu id bisa punya banyak dokumen
        return `${v.id}||${v.stock_dokumen || ''}||${v.bc_id || ''}`;
    }

    // GET & MERGE tanpa menghapus listStockAsal
    function getListDokumenPabean() {
        $.ajax({
            url: `<?= base_url('jasa-vendor-out/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() { setLoading(); },
            complete: function() { stopLoading(); },
            data: {
                barang_master_id: $(".spesifikasi_id option:selected").data('barang_master_id'),
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
                supplier_id: $('#supplier_id').select2('val'),
                vendor_id: $('.vendor_barang_id option:selected').val()
            },
            dataType: "json",
            success: function(res) {
                var typeAsalBarang = $('#type_asal_barang option:selected').val();

                if (!Array.isArray(listStockAsal)) listStockAsal = [];

                $.each(res.data, function(i, v) {
                    // skip JASA VENDOR saat rule SUPPLIER
                    if (v.sumber === "JASA VENDOR" && typeAsalBarang === "SUPPLIER") {
                        return;
                    }

                    // normalisasi stok_total
                    v.stok_total = parseFloat(v.stok_total) || 0;

                    // cari apakah sudah ada di listStockAsal
                    var existingItem = listStockAsal.find(function(item) {
                        return item.id === v.id && 
                            item.stock_dokumen === v.stock_dokumen && 
                            item.bc_id === v.bc_id;
                            item.no_aju === v.no_aju;
                    });

                    if (!existingItem) {
                        // tambahkan baru
                        listStockAsal.push(v);
                    } else {
                        // update yang sudah ada
                        existingItem.stok_total = v.stok_total;
                        existingItem.sumber = v.sumber;
                        existingItem.supplier_name = v.supplier_name;
                        existingItem.bc_type = v.bc_type;
                        existingItem.stock_date = v.stock_date;
                        existingItem.barang = v.barang;
                        existingItem.satuan = v.satuan;
                    }
                });

                drawTableAsalBarang(listStockAsal);
            }
        });
    }

    function drawTableAsalBarang(data) {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            dataTable = $('#dataTable').DataTable({
                processing: false,
                serverSide: false,
                ordering: true,
                order: [],
                fixedHeader: true,
                initComplete: function() {
                    $('.dataTables_length').empty();
                    $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
                    $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
                display: "stripe",
                searching: true,
                lengthMenu: [[100],[100]],
                language: {
                    emptyTable: "Tidak Ada Data",
                    lengthMenu: "Show _MENU_ entries",
                    paginate: {
                        previous: '<i class="fa fa-angle-left"></i>',
                        next: '<i class="fa fa-angle-right"></i>'
                    }
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Parsing angka
                    var intVal = function (i) {
                        if (typeof i === 'string') {
                            return parseFloat(i.replace(/,/g, '')) || 0;
                        }
                        if (typeof i === 'number') {
                            return i;
                        }
                        return 0;
                    };

                    // Total keseluruhan
                    var total = api
                        .column(8) // kolom Qty (0-based)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Total per halaman
                    var pageTotal = api
                        .column(8, { page: 'current'} )
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    // Update footer
                    $(api.column(8).footer()).html(
                        pageTotal.toFixed(2) + ' (Total: ' + total.toFixed(2) + ')'
                    );
                }
            });
        } else {
            dataTable.clear();
        }

        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        var totalQty = 0;

        data.forEach(function(v) {
            totalQty += parseFloat(v.stok_total) || 0;
            var row = $('<tr>');
            
            if (typePengambilanStok == "FIFO" || v.stok_total == 0) {
                row.append($('<td style="text-align: center;">').html(''));
            } else {
                row.append($('<td style="text-align: center;">').html(
                    `<div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" 
                            class="form-check-input child" type="checkbox"
                            style="transform: scale(2); margin: 8px;">
                    </div>`
                ));

            }

            row.append($('<td style="text-align:center;">').text(v.sumber || '-'));
            row.append($('<td style="text-align:center;">').text(v.stock_dokumen || '-'));
            row.append($('<td style="text-align:center;">').text(v.supplier_name || '-'));
            row.append($('<td style="text-align:center;">').text(v.bc_type || '-'));
            row.append($('<td style="text-align:center;">').text(v.stock_date || '-'));
            row.append($('<td style="text-align:center;">').text(v.barang || '-'));
            row.append($('<td style="text-align:center;">').text(v.satuan || '-'));
            row.append($('<td style="text-align:center;">').text(greatFormatRupiah(v.stok_total) || 0));

            dataTable.row.add(row);
        });
        
        dataTable.draw(false);
        $('#dataTable tfoot th:last').text(totalQty.toFixed(2)); // 2 angka di belakang koma
    }

    function drawTableSelectedItem(data) {
        console.log(data)
        var typePengambilanStok = $('#type_pengambilan_stock option:selected').val();
        const table = $('#selectedItemTable');
        var no = 1;
        $('.foot-detail-table').empty();
        $('.body-table').empty();

        if (data.length == 0) {
            var newRow = '';
            newRow += `
                    <tr>
                        <td colspan="10" style="text-align: center;">
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
                newRow.append($('<td style="text-align: center;">').text(v.keterangan));
                // newRow.append($('<td style="text-align: center;">').text(v.bc_type + '/' + v.no_aju));
                newRow.append($('<td style="text-align: center;">').text(v.stock_date));
                newRow.append($('<td style="text-align: center;">').text(v.barang));
                newRow.append($('<td style="text-align: center;">').text(v.satuan));
                newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(v.stok_total)));
                if (typePengambilanStok == "FIFO") {
                    newRow.append($('<td style="text-align: center;">').text(greatFormatRupiah(v.qty)));
                } else {
                    newRow.append($('<td style="text-align: center;">').html(
                        `
                    <input <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> onkeyup="this.value = greatFormatRupiah(this.value)" class="form-control stok-out" oninput="preventNegativeInput(this)" autocomplete="one-time-code" data-id="${v.id}" data-stok_total="${v.stok_total}" class="form-control" type="text" value="${greatFormatRupiah(v.qty)}">
                `
                    ));
                }

                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($jasaVendorOut) ? (($jasaVendorOut['status_posting'] == "1") ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger" onclick="deleteDetail(${v.id})" ><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                `
                ));
                table.find('tbody').append(newRow);

                totalQtyKeluar += destroyFormatRupiah(v.qty);
            });

            var newRow = $('<tr class="grand-total" style="color:whitesmoke; background-color:#f2c996;">');
            newRow.append($('<td style="text-align: right;" colspan="9">').html("<b>GRAND TOTAL</b>"));
            newRow.append($('<td class="total-cell">').text(greatFormatQty(totalQtyKeluar)));
            newRow.append($('<td>').text(''));
            table.find('tbody').append(newRow);

            $(document).on("input", ".stok-out", function() {
                updateGrandTotal();
            });
        }
    }
    

    function deleteDetail(id) {
        // Pastikan id jadi angka biar perbandingan aman
        id = Number(id);

        // Filter list, sisakan item yang ID-nya beda
        listStockSelected = listStockSelected.filter(item => Number(item.id) !== id);

        // Gambar ulang tabel
        drawTableSelectedItem(listStockSelected);
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
                    warehouse_id: $('#warehouse_id option:selected').val(),
                    tanggal: $('#tanggal').val()
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

    function getDropdownAsalBarang() {
        var typeAsalBarang = $('#type_asal_barang option:selected').val();
        if (typeAsalBarang == 'SUPPLIER') {
            $('#supplier_id_select').show();
            $('#vendor_barang_id_select').hide();
        } else {
            $('#supplier_id_select').hide();
            $('#vendor_barang_id_select').show();
        }
        $('#supplier_id').val(null).change();
        $('#vendor_barang_id').val(null).change();
        $('#warehouse_id').change();

    }

    // fungsi hitung ulang total
    function updateGrandTotal() {
        let total = 0;
        $('.stok-out').each(function () {
            total += destroyFormatRupiah($(this).val());
        });
        // tampilkan clean (ga perlu paksa ".00", kalau mau tambahin ya boleh)
        $('#selectedItemTable tbody tr.grand-total td.total-cell')
            .text(greatFormatQty(total));
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