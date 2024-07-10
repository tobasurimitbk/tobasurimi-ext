<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($salesOrderLain) ? "Tambah Order Form Lain" : "Update Order Form Lain" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("order-form-lain"); ?>">
                Kembali
            </a>
            <?php if (!empty($salesOrderLain)) : ?>
                <?php if ($salesOrderLain['status_posting'] == "0") : ?>
                    <?php if (can('Penjualan Lain', 'Order Form', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right" onclick="remove('<?= encrypt($salesOrderLain['id']); ?>')">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Penjualan Lain', 'Order Form', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi" onclick="posting('<?= encrypt($salesOrderLain['id']); ?>')">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Penjualan Lain', 'Order Form', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("order-form-lain/print/"); ?><?= encrypt($salesOrderLain['id']); ?>')">
                            Print
                        </button>
                    <?php endif; ?>
                    <?php if (can('Penjualan Lain', 'Order Form', 'u')) : ?>
                        <button class="btn btn-show-form btn-save float-right btn-submit-parent">
                            Simpan
                        </button>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (can('Penjualan Lain', 'Order Form', 'p')) : ?>
                        <button class="btn btn-warning btn-print float-right" onclick="print('<?= base_url("order-form-lain/print/"); ?><?= encrypt($salesOrderLain['id']); ?>')">
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
                    <label class="form-label font-weight-bold lable-title">Data Sales Order</label>
                </div>
            </div>
            <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?= !empty($salesOrderLain) ? encrypt($salesOrderLain['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" <?= !empty($salesOrderLain) ? 'disabled=true' : ''; ?> value="<?= !empty($salesOrderLain) ? $salesOrderLain['no_sales_order'] : "SOL//" . date('m') . "/1/" . date('Y'); ?>" type="text" class="form-control no_sales_order" id="no_sales_order" name="no_sales_order" placeholder="No Sales Order">
                                    <label for="floatingInput">No. Sales Order</label>
                                </div>
                                <div style="<?= !empty($salesOrderLain) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($salesOrderLain) ? $salesOrderLain['tanggal'] : $tanggal)); ?>">
                                    <label for="floatingInput">Tanggal Order</label>
                                </div>
                                <div class="input-group-prepend group-prepend-password align-items-center">
                                    <i style="cursor: pointer; z-index: 99; margin-bottom: 8px; margin-left: -30px; border: 0px" class="fa fa-calendar icon-form icon-po-date"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($salesOrderLain) ? ($salesOrderLain['divisi_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_id" id="warehouse_id" name="warehouse_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($salesOrderLain)) : ?>
                                    <option value="<?= $salesOrderLain['warehouse_id'] ?>" selected>
                                        <?= $salesOrderLain['warehouse_name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> class="form-select bc_id" id="bc_id" name="bc_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dokumenPabean as $d) : ?>
                                    <option <?= !empty($salesOrderLain) ? ($salesOrderLain['bc_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['value'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Dokumen Bea Cukai</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> class="form-select tipe_customer" id="tipe_customer" name="tipe_customer" aria-label="Floating label select example">
                                <option value=""></option>
                                <option <?= !empty($salesOrderLain) ? ($salesOrderLain['tipe_customer'] == "LOKAL" ? 'selected' : '') : '' ?> value="LOKAL">LOKAL</option>
                                <option <?= !empty($salesOrderLain) ? ($salesOrderLain['tipe_customer'] == "INTERNASIONAL" ? 'selected' : '') : '' ?> value="INTERNASIONAL">INTERNASIONAL</option>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Tipe Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> class="form-select customer_id" id="customer_id" name="customer_id" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php if (!empty($salesOrderLain)) : ?>
                                    <option selected value="<?= $salesOrderLain['customer_id'] ?>">
                                        <?= $salesOrderLain['kode_customer'] . " - " . $salesOrderLain['customer_name'] ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Pilih Customer</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($salesOrderLain) ? $salesOrderLain['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" aria-label="Floating label select example" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="detail-form-layout">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label font-weight-bold lable-title">Pilih Barang Yang Akan Dijual </label>
                    </div>
                </div>
                <form class="detail-form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select type_barang" id="type_barang" name="type_barang" aria-label="Floating label select example">
                                    <option value=""></option>
                                    <?php foreach ($tipeBarang as $t) : ?>
                                        <?php if ($t['description'] != "bahan_jadi" && $t['description'] != "bahan_setengah_jadi" &&  $t['description'] != "bahan_modal") : ?>
                                            <option value="<?= $t['description'] ?>">
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
                                <label for="floatingInput" style="z-index: 1;">Barang - Spesifikasi (Yang Akan Dijual)</label>
                            </div>
                        </div>

                    </div>
                </form>


                <div class="row mt-3">
                    <div class="col mb-0">
                        <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang yang Akan Dijual</label>
                    </div>
                    <div class="col-md-12 col-table-button-tts" style="margin-top: 10px;">
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi table-form-tts dataTable" id="dataTable" width="100%" cellspacing="0">
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
                    <label class="form-label font-weight-bold lable-title">Daftar Barang yang Akan Dijual</label>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable2" width="100%" border="1" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="text-align: center;" colspan="8">Detail Barang yang Dijual</th>
                                    <th style="text-align: center;" colspan="7">Data Harga</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">No</th>
                                    <th style="text-align: center;">Asal Barang</th>
                                    <th style="text-align: center;">No Dokumen</th>
                                    <th style="text-align: center;">Supplier</th>
                                    <th style="text-align: center;">Dokumen Pabean</th>
                                    <th style="text-align: center;">Tgl Penerimaan</th>
                                    <th style="text-align: center;">Barang - Spesifikasi</th>
                                    <th style="text-align: center;">Qty Stok</th>

                                    <th style="text-align: center;">Qty Order</th>
                                    <th style="text-align: center;">Qty Konversi</th>
                                    <th style="text-align: center;">Harga Satuan</th>
                                    <th style="text-align: center;">Potongan Harga</th>
                                    <th style="text-align: center;">Biaya Tambahan</th>
                                    <th style="text-align: center;">Total Harga</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot class="foot-detail-table" id="foot-detail-table">
                                <tr>
                                    <td colspan="15" style="text-align: center;">
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
<div class="modal add-modal" id="updateHargaDetailModal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail List Barang Order Form</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-detail" role="form" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="stock_detail2_id" class="stock_detail2_id" id="stock_detail2_id">
                    <input type="hidden" name="stock_id" id="stock_id" class="stock_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control barang_dijual_name" id="barang_dijual_name" name="barang_dijual_name" placeholder="Barang Dijual">
                                <label for="floatingInput">Barang Dijual</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control supplier_name" id="supplier_name" name="supplier_name" placeholder="Supplier Name">
                                <label for="floatingInput">Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control tanggal_penerimaan" id="tanggal_penerimaan" name="tanggal_penerimaan" placeholder="Tanggal Penerimaan">
                                <label for="floatingInput">Tanggal Penerimaan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control no_po" id="no_po" name="no_po" placeholder="Nomor Purchase Order">
                                <label for="floatingInput">No PO</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input autocomplete="one-time-code" readonly type="text" class="form-control dokumen_pabean" id="dokumen_pabean" name="dokumen_pabean" placeholder="Dokumen Pabean">
                                <label for="floatingInput">Dokumen Pabean</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3">
                                    <input autocomplete="one-time-code" readonly type="text" class="form-control qty_stok" id="qty_stok" name="qty_stok" placeholder="Qty Stok Name">
                                    <label for="floatingInput">Qty Stok</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button class="btn btn-primary satuan_inventori_name" id="satuan_inventori_name" type="button">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="preventNegativeInput(this)" autocomplete="one-time-code" type="text" class="form-control qty_order" id="qty_order" name="qty_order" placeholder="Qty Order">
                                    <label for="floatingInput" style="z-index: 1;">Qty Order</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <div class="form-floating">
                                        <select <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] == '1' ? 'disabled' : '') : '' ?> class="form-select satuan_order_id" name="satuan_order_id" id="satuan_order_id" style="padding-top: 5px; padding-bottom: 5px; line-height: 1.5; height: 50px;">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="input-group-append" style="height:50px;">
                                    <button data-toggle="tooltip" data-placement="top" title="Hitung Hasil Konversi" class="btn btn-primary btn-konversi-stok" id="btn-konversi-stok" type="button">
                                        <i class="fa-solid fa-money-bill-1-wave"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="form-floating mb-3">
                                    <input autocomplete="one-time-code" readonly type="text" class="form-control qty_konversi" id="qty_konversi" name="qty_konversi" placeholder="Qty Konversi">
                                    <label for="floatingInput">Qty Konversi</label>
                                </div>
                                <div class="input-group-append" style="height:50px;">
                                    <button class="btn btn-primary satuan_konversi_name" id="satuan_konversi_name" type="button">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input onchange="this.value = formatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control harga_satuan" id="harga_satuan" name="harga_satuan" placeholder="Harga Satuan">
                                <label for="floatingInput">Harga Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input onchange="this.value = formatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control potongan_harga" id="potongan_harga" name="potongan_harga" placeholder="Potongan Harga">
                                <label for="floatingInput">Potongan Harga</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input onchange="this.value = formatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control biaya_tambahan" id="biaya_tambahan" name="biaya_tambahan" placeholder="Biaya Tambahan">
                                <label for="floatingInput">Biaya Tambahan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input readonly autocomplete="one-time-code" type="text" class="form-control total_harga" id="total_harga" name="total_harga" placeholder="Total Harga">
                                <label for="floatingInput">Total Harga</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard btn-discard-detail mr-2">Kembali</button>
                <?php if (!empty($salesOrderLain)) : ?>
                    <?php if ($salesOrderLain['status_posting'] != '1') : ?>
                        <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                    <?php else : ?>

                    <?php endif; ?>
                <?php else : ?>
                    <button type="submit" class="btn btn-submit-form btn-submit-detail">Simpan</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var listStockAsal = [];
    var listStockSelected = [];

    // INIT PAS UPDATE
    <?php if (!empty($salesOrderLain)) : ?>
        <?php if ($salesOrderLain['status_posting'] === "1") : ?>
            $('.detail-form-layout').hide();
        <?php endif; ?>
        $.ajax({
            url: `<?= base_url('order-form-lain/list-sales-order-detail'); ?>`,
            method: "GET",
            data: {
                sales_order_lain_id: "<?= encrypt($salesOrderLain['id']) ?>"
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockSelected = [];
                listStockSelected = res.data;
                // DRAWTABLE
                drawTableSelectedItem(listStockSelected);
            }
        });
    <?php endif; ?>

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


    $(".tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen Penjualan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // RESET
        listStockAsal = [];
        listStockSelected = [];
        // DROPDOWN WAREHOUSE
        getListWarehouse();
        // DROPDOWN BARANG
        getListBarang();
        // GENERATE NOMOR
        changeStatus();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse Penjualan",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // RESET
        listStockAsal = [];
        listStockSelected = [];
        // DROPDOWN BARANG
        getListBarang();
    });

    $('#bc_id').select2({
        placeholder: "Pilih Dokumen Bea Cukai",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // RESET
        listStockAsal = [];
        listStockSelected = [];
        // DROPDOWN DOKUMEN PABEAN
        getListBarang();
        drawTableAsalBarang(listStockAsal);
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // DROPDOWN BARANG
        getListBarang();
    });

    $('#spesifikasi_id').select2({
        placeholder: "Pilih Barang - Spesifikasi",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // DROPDOWN DOKUMEN PABEAN
        getListDokumenPabean();

    });

    $('#tipe_customer').select2({
        placeholder: "Pilih Tipe Customer",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // DROPDOWN CUSTOMER
        getListCustomer();
    });

    $('#customer_id').select2({
        placeholder: "Pilih Customer",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {

    });

    $("#divisi_id,#warehouse_id,#bc_id,#type_barang,#spesifikasi_id,#tipe_customer,#customer_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#select-item-btn').click(function() {
        insertListPabean();
    });

    $('.btn-discard-detail').click(function() {
        $('#updateHargaDetailModal').modal('hide');
    });

    var validator = $(".create-form").validate({
        rules: {
            no_sales_order: {
                required: true
            },
            tanggal: {
                required: true
            },
            divisi_id: {
                required: true
            },
            warehouse_id: {
                required: true
            },
            bc_id: {
                required: true
            },
            tipe_customer: {
                required: true
            },
            customer_id: {
                required: true
            },
        },
        messages: {
            no_sales_order: {
                required: "No sales order wajib diisi"
            },
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            warehouse_id: {
                required: "Warehouse wajib diisi"
            },
            bc_id: {
                required: "Dokumen pabean wajib dipilih"
            },
            tipe_customer: {
                required: "Pilih tipe customer"
            },
            customer_id: {
                required: "Pilih customer"
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

    var validatorDetail = $(".create-form-detail").validate({
        rules: {
            qty_order: {
                required: true
            },
            satuan_order_id: {
                required: true
            },
            qty_konversi: {
                required: true
            },
            harga_satuan: {
                required: true
            },
            potongan_harga: {
                required: true
            },
            biaya_tambahan: {
                required: true
            },
            total_harga: {
                required: true
            },
        },
        messages: {
            qty_order: {
                required: "Qty order wajib diisi"
            },
            satuan_order_id: {
                required: "Satuan order wajib diisi"
            },
            qty_konversi: {
                required: "Qty konversi wajib diisi"
            },
            harga_satuan: {
                required: "Harga satuan wajib diisi"
            },
            potongan_harga: {
                required: "Potongan harga wajib diisi"
            },
            biaya_tambahan: {
                required: "Biaya tambahan wajib diisi"
            },
            total_harga: {
                required: "Total harga wajib diisi"
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

    // SIMPAN DETAIL
    $('.btn-submit-detail').click(function() {
        if ($('.create-form-detail').valid()) {
            var totalHarga = convertRupiahToNumber($('#total_harga').val());
            var qtyKonversi = parseFloat($('#qty_konversi').val());
            var id = $('#stock_detail2_id').val();

            if (qtyKonversi <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Barang yang keluar harus lebih dari 0 !",
                    confirmButtonColor: '#4e73df',
                })
            } else if (totalHarga <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Total harga harus lebih dari 0 !",
                    confirmButtonColor: '#4e73df',
                })
            } else {
                var index = null;
                for (let i = 0; i < listStockSelected.length; i++) {
                    if (listStockSelected[i].id == id) {
                        index = i;
                        break;
                    }
                }

                var hargaSatuan = convertRupiahToNumber($('#harga_satuan').val()) || 0;
                var potonganHarga = convertRupiahToNumber($('#potongan_harga').val()) || 0;
                var biayaTambahan = convertRupiahToNumber($('#biaya_tambahan').val()) || 0;
                listStockSelected[index].satuan_order_id = $('#satuan_order_id option:selected').val();
                listStockSelected[index].satuan_order_text = $('#satuan_order_id option:selected').text();
                listStockSelected[index].qty_order = $('#qty_order').val();
                listStockSelected[index].qty_konversi = qtyKonversi;
                listStockSelected[index].harga_satuan = hargaSatuan;
                listStockSelected[index].potongan_harga = potonganHarga;
                listStockSelected[index].biaya_tambahan = biayaTambahan;
                listStockSelected[index].total_harga = totalHarga;

                drawTableSelectedItem(listStockSelected);
                $('#updateHargaDetailModal').modal('hide');
            }
        }
    });

    // SIMPAN ATAS
    $('.btn-submit-parent').click(function(e) {
        e.preventDefault();
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: "List barang yang akan dijual tidak boleh kosong !",
                confirmButtonColor: '#4e73df',
            })
        } else {
            if ($('.create-form').valid()) {
                var error = null;
                $.each(listStockSelected, function(i, v) {
                    if (v.satuan_order_text == "") {
                        error = v;
                    }
                });
                if (error != null) {
                    Swal.fire({
                        icon: 'error',
                        title: "Barang " + error.barang + " dengan no dokumen " + error.stock_dokumen + ", detail harganya belum diisi !",
                        confirmButtonColor: '#4e73df',
                    })
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
                            var id = $('#id').val();
                            var data = new FormData(document.querySelector(".create-form"));
                            data.append('listBarang', JSON.stringify(listStockSelected));

                            if (id) {
                                // UPDATE
                                $.ajax({
                                    url: "<?= base_url("order-form-lain/update"); ?>",
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
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("order-form-lain") ?>";
                                                }
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
                            } else {
                                // CREATE
                                $.ajax({
                                    url: "<?= base_url("order-form-lain/save"); ?>",
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
                                        if (response.status) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                                confirmButtonText: 'Ok'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = "<?= base_url("order-form-lain") ?>";
                                                }
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
                        }
                    })

                }
            }
        }

    });

    // KEYUP HARGA
    $('#harga_satuan,#potongan_harga,#biaya_tambahan').keyup(function() {
        if (qtyKonversi != "") {
            var qtyKonversi = parseFloat($('#qty_konversi').val());
            var hargaSatuan = convertRupiahToNumber($('#harga_satuan').val());
            var potonganHarga = convertRupiahToNumber($('#potongan_harga').val());
            var biayaTambahan = convertRupiahToNumber($('#biaya_tambahan').val());

            var total = (qtyKonversi * hargaSatuan) - potonganHarga + biayaTambahan;
            $('#total_harga').val(formatRupiah(total));
        } else {
            $('#total_harga').val(formatRupiah(0));
        }
    });

    $('#btn-konversi-stok').click(function() {
        var qtyOrder = parseFloat($('#qty_order').val());
        var satuanOrderId = $(".satuan_order_id option:selected").val();

        if (satuanOrderId == "") {
            Swal.fire({
                icon: 'error',
                title: "Satuan order wajib diisi !",
                confirmButtonColor: '#4e73df',
            })
        } else if (isNaN(qtyOrder) || qtyOrder == undefined || qtyOrder < 0) {
            Swal.fire({
                icon: 'error',
                title: "Qty order wajib diisi !",
                confirmButtonColor: '#4e73df',
            })
        } else {
            hitungKonversi();
        }
    });

    function insertListPabean() {
        var checkedCheckboxes = $(".child:checked");
        var dataIds = checkedCheckboxes.map(function() {
            return $(this).data("id");
        }).get();

        if (dataIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: "Pilih minimal satu barang yang akan dijual !",
                confirmButtonColor: '#4e73df',
            })
        } else {
            $.each(listStockAsal, function(i, v) {
                var currentID = Number(v.id);
                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelected, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;

                    if (!isIDSelected) {
                        listStockAsal[i].stok_total = parseFloat(listStockAsal[i].stok_total);
                        listStockSelected.push(listStockAsal[i]);
                    }
                }
            });
            // reset barang kirim ke vendor dan list bc nya
            $('#spesifikasi_id').val(null).change();
            drawTableSelectedItem(listStockSelected);
            drawTableAsalBarang([]);
        }

    }


    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    function drawTableAsalBarang(data) {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().clear().draw();
            dataTable.destroy();
        }
        const table = $('#dataTable');

        $.each(data, function(i, v) {
            var newRow = $('<tr>');

            newRow.append($('<td style="text-align: center;">').html(
                `
                    <div class="form-check">
                        <input data-id="${v.id}" data-stok_total="${v.stok_total}" autocomplete="one-time-code" class="form-check-input child" type="checkbox">
                    </div>
                `
            ));
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
        var no = 1;
        const table = $('#dataTable2');
        table.find('tbody').empty();
        table.find('tfoot').empty();

        if (data.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td colspan="15" style="text-align:center">Tidak Ada Barang</td>'));
            table.find('tfoot').append(newRow);
        } else {
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
                newRow.append($('<td style="text-align: center;">').text(v.stok_total + " " + v.satuan));
                newRow.append($('<td style="text-align: center;">').text(v.qty_order + " " + v.satuan_order_text));
                newRow.append($('<td style="text-align: center;">').text(v.qty_konversi + " " + v.satuan));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.harga_satuan)));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.potongan_harga)));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.biaya_tambahan)));
                newRow.append($('<td style="text-align: center;">').text(formatRupiah(v.total_harga)));
                newRow.append($('<td style="text-align: center;">').html(
                    `
                    <button <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] === "1" ? 'disabled' : '') : '' ?> type="button" class="btn btn-danger mr-1" onclick="deleteDetail(${v.id})"><i class="fa fa-trash fa-sm" aria-hidden="true"></i></button>
                    <button <?= !empty($salesOrderLain) ? ($salesOrderLain['status_posting'] === "1" ? 'disabled' : '') : '' ?> type="button" class="btn btn-primary" onclick="displayDetail(${v.id})" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></button>
                `
                ));
                table.find('tbody').append(newRow);
            });

        }
    }

    function deleteDetail(id) {
        var indexToRemove = -1;
        for (let i = 0; i < listStockSelected.length; i++) {
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

    function displayDetail(id) {
        var first = null;
        var index = null;

        for (let i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                index = i;
                break;
            }
        }
        first = listStockSelected[index];
        $('#stock_id').val(first.stock_id);
        $('#stock_detail2_id').val(first.id);
        // CARI SATUAN KONVERSI DROPDOWN
        $.ajax({
            url: `<?= base_url('order-form-lain/list-satuan-konversi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: first.stock_id,
            },
            dataType: "json",
            success: function(res) {
                $(".satuan_order_id").empty()
                $(".satuan_order_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".satuan_order_id").append(`<option value="${item.id}">${item.kode_satuan}</option>`)
                })
                $(".satuan_order_id").val();


                $('#barang_dijual_name').val(first.barang);
                $('#supplier_name').val(first.supplier_name);
                $('#tanggal_penerimaan').val(first.stock_date);
                $('#no_po').val(first.no_po);
                if (first.bc_type != "NON PABEAN") {
                    $('#dokumen_pabean').val(first.bc_type + " / " + first.no_aju);
                } else {
                    $('#dokumen_pabean').val(first.bc_type);
                }
                $("#satuan_order_id").val(first.satuan_order_id);
                $('#qty_stok').val(parseFloat(first.stok_total));
                $('#qty_order').val(parseFloat(first.qty_order));
                $('#qty_konversi').val(parseFloat(first.qty_konversi));

                $('#harga_satuan').val(formatRupiah(first.harga_satuan));
                $('#potongan_harga').val(formatRupiah(first.potongan_harga));
                $('#biaya_tambahan').val(formatRupiah(first.biaya_tambahan));
                $('#total_harga').val(formatRupiah(first.total_harga));

                $('#satuan_inventori_name').text(first.satuan);
                $('#satuan_konversi_name').text(first.satuan);

                $('#updateHargaDetailModal').modal('show');
            }
        });

    }

    function hitungKonversi() {
        $.ajax({
            url: `<?= base_url('order-form-lain/hitung-konversi'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".stock_id").val(),
                qty_stock: $(".qty_stok").val(),
                qty_order: $(".qty_order").val(),
                satuan_order_id: $(".satuan_order_id option:selected").val()
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $('#qty_konversi').val(res.qty_konversi);
                } else {
                    $('#qty_konversi').val("");
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        });
    }

    function getListBarang() {
        // GET LIST BARANG
        $.ajax({
            url: `<?= base_url('order-form-lain/list-stock-init'); ?>`,
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
            url: `<?= base_url('order-form-lain/list-stock-dokumen-bc'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                stock_id: $(".spesifikasi_id option:selected").data('stock_id'),
                bc_id: $('.bc_id').val()
            },
            dataType: "json",
            success: function(res) {
                // LIST STOK PER BC
                listStockAsal = [];
                listStockAsal = res.data;
                // DRAWTABLE
                drawTableAsalBarang(listStockAsal);
            }
        });
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

    function getListCustomer() {
        $.ajax({
            url: `<?= base_url('order-form-lain/list-customer'); ?>`,
            method: "GET",
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            data: {
                tipe_customer: $(".tipe_customer option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".customer_id").empty()
                $(".customer_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".customer_id").append(`<option value="${item.id}"> ${item.kode} - ${item.name}</option>`)
                })
                $(".customer_id").val();
            }
        });
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

    function formatRupiah(angka) {
        var formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        var parsedNumber = parseFloat(angka);
        if (isNaN(parsedNumber)) {
            return "0,00";
        }
        return formatter.format(parsedNumber).replace('Rp', '').trim();
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

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        if (value) {
            $(".no_sales_order").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("order-form-lain/get-no"); ?>`,
                method: "GET",
                data: {
                    divisi_id: $('#divisi_id option:selected').val()
                },
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".no_sales_order").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $(".no_sales_order").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $(".no_sales_order").val("");
                    }
                }
            })
        } else {
            $(".no_sales_order").attr("readonly", false);
            $(".no_sales_order").val("");
        }
    }

    const print = function(url) {
        window.open(url, "_blank");
    }

    const posting = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Posting Order Form ?',
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
                    url: "<?= base_url("order-form-lain/posting"); ?>",
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
                                window.location.href = "<?= base_url("order-form-lain") ?>";
                            });
                        }
                    },
                });
            }
        })
    }

    const remove = function(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Order Form ?',
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
                    url: "<?= base_url("order-form-lain/delete"); ?>",
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
                                window.location.href = "<?= base_url("order-form-lain") ?>";
                            });
                        }
                    },
                });
            }
        })
    }
</script>

<?= $this->endSection(); ?>