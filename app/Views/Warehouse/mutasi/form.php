<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1><?= empty($mutasi) ? "Tambah Mutasi PPBKB" : "Update Mutasi PPBKB" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("mutasi"); ?>">
                Kembali
            </a>
            <?php if (!empty($mutasi)) : ?>
                <?php if ($mutasi['status_posting'] == "0") : ?>
                    <?php if (can('Inventori', 'Mutasi', 'd')) : ?>
                        <button class="btn btn-hapus delete-parent float-right">
                            Hapus
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Mutasi', 'a')) : ?>
                        <button class="btn btn-success posting-spp float-right posting-mutasi">
                            Posting
                        </button>
                    <?php endif; ?>
                    <?php if (can('Inventori', 'Mutasi', 'u')) : ?>
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

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Mutasi PPBKB</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('mutasi/create-global') ?>">Mutasi BC 2.7</a>
                </li>
            </ul>

            <form class="create-form mt-3">
                <input type="hidden" name="id" id="id" value="<?= !empty($mutasi) ? encrypt($mutasi['id']) : '' ?>" class="id">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <div class="input-group input-group-password">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($mutasi) ? $mutasi['tanggal'] : $tanggal)); ?>">
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
                                    <input autocomplete="one-time-code" <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> value="<?= !empty($mutasi) ? $mutasi['no_mutasi'] : ""; ?>" type="text" class="form-control no_mutasi" id="no_mutasi" name="no_mutasi" placeholder="No. mutasi">
                                    <label for="floatingInput">No. Mutasi</label>
                                </div>
                                <div style="<?= !empty($mutasi) ? "display: none" : ""; ?>" class="input-generate input-group-prepend group-prepend-password align-items-center">
                                    <input checked autocomplete="one-time-code" style="z-index: 99; margin-bottom: 20px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_asal_id" id="divisi_asal_id" name="divisi_asal_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($mutasi) ? ($mutasi['divisi_asal_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Asal</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_asal_id" id="warehouse_asal_id" name="warehouse_asal_id">
                                <option value=""></option>
                                <?php if (!empty($warehouseAsal)) : ?>
                                    <?php foreach ($warehouseAsal as $w) : ?>
                                        <option <?= !empty($mutasi) ? ($mutasi['warehouse_asal_id'] == $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse Asal</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_tujuan_id" id="divisi_tujuan_id" name="divisi_tujuan_id">
                                <option value=""></option>
                                <?php foreach ($divisi as $d) : ?>
                                    <option <?= !empty($mutasi) ? ($mutasi['divisi_asal_id'] == $d['id'] ? 'selected' : '') : '' ?> value="<?= $d['id'] ?>">
                                        <?= $d['divisi']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Departemen Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> class="form-select warehouse_tujuan_id" id="warehouse_tujuan_id" name="warehouse_tujuan_id">
                                <option value=""></option>
                                <?php if (!empty($warehouseTujuan)) : ?>
                                    <?php foreach ($warehouseTujuan as $w) : ?>
                                        <option <?= !empty($mutasi) ? ($mutasi['warehouse_tujuan_id'] == $w['id'] ? 'selected' : '') : '' ?> value="<?= $w['id'] ?>">
                                            <?= $w['warehouse_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse Tujuan</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($mutasi) ? ($mutasi['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($mutasi) ? $mutasi['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">Daftar Barang Yang Akan Dipindahkan</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" type="button" id="btnDetailStockModal">
                            <i class="fa-solid fa-magnifying-glass"></i> Inventori
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-5">
                    <div class="table-responsive" style="margin-top: -10px;">
                        <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="selectedItemTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Sumber</th>
                                    <th>Supplier / Vendor</th>
                                    <th>Kode</th>
                                    <th>Barang</th>
                                    <th>Spesifikasi</th>
                                    <th>Doc</th>
                                    <th>No Po</th>
                                    <th>Tgl Po</th>
                                    <th>Tgl Masuk</th>
                                    <th>Ref No</th>
                                    <th>Stok Awal</th>
                                    <th>Qty Mutasi</th>
                                    <th>Satuan Mutasi</th>
                                    <th>Qty Konversi</th>
                                    <th>Satuan Konversi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="body-table">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="17">Tidak Ada Data</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


        </div>

    </div>
</section>

<div class="modal detail-modal" id="detailStockModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 100rem !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Cari Stok Barang</h5>
            </div>
            <div class="modal-body">
                <div class="detail-form-component">
                    <div class="detail-form-layout">
                        <label class="form-label font-weight-bold lable-title" id="cari_stock_title">Pilih Tipe Ambil Stok</label>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select type_pengambilan_stock" id="type_pengambilan_stock" name="type_pengambilan_stock">
                                        <option value=""></option>
                                        <option value="PABEAN">PABEAN</option>
                                        <option value="FIFO">FIFO</option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Tipe Pengambilan Stok</label>
                                </div>
                            </div>
                            <div class="col-md-4 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Qty Mutasi" oninput="this.value = greatFormatRupiah(this.value)" class="form-control qty_mutasi_fifo" id="qty_mutasi_fifo" name="qty_mutasi_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Qty Mutasi</label>
                                </div>
                            </div>
                        </div>

                        <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang Yang Akan Anda Mutasikan</label>
                        <div class="row mt-3 justify-content-left">
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating" style="height: 50px;">
                                    <select class="form-select type_barang" id="type_barang" name="type_barang">
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
                            <div class="col-sm-3 mb-2">
                                <div class="input-group">
                                    <div class="form-floating" style="height: 50px;">
                                        <input value="01/09/2025" placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" />
                                        <label style="z-index: 1;" style="z-index: 1;">Tgl Awal Masuk</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button disabled class="btn btn-secondary" type="button">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="input-group">
                                    <div class="form-floating" style="height: 50px;">
                                        <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                                        <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir Masuk</label>
                                    </div>
                                    <div class="input-group-append" style="height:50px;">
                                        <button disabled class="btn btn-secondary" type="button">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating" style="height: 50px;">
                                    <select class="form-select spesifikasi_id" id="spesifikasi_id" name="spesifikasi_id">
                                        <option value=""></option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Cari Barang</label>
                                </div>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input placeholder="Cari Data" value="" class="form-control search" id="search" name="search" />
                                    <label for="floatingInput" style="z-index: 1;">Cari Data</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-table-button-tts" style="margin-top: 10px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-inventori" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>#</th>
                                                <th>Dept</th>
                                                <th>Warehouse</th>
                                                <th>Sumber</th>
                                                <th>Supplier / Vendor</th>
                                                <th>Kode Barang</th>
                                                <th>Barang</th>
                                                <th>Spesifikasi</th>
                                                <th>Doc</th>
                                                <th>No Po</th>
                                                <th>Tgl Po</th>
                                                <th>Tgl Masuk</th>
                                                <th>Ref No</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table" id="body-detail-list-inventori">

                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" id="select-item-btn">
                                        Pilih Inventori
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard mr-2" id="btnHideDetailStock">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="detailMutasiModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Input Qty Mutasi</h5>
            </div>
            <form class="update-form-mutasi" role="form" method="POST">
                <input type="hidden" name="id_stock_detail" id="id_stock_detail" class="id_stock_detail">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control kode_barang" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                    <label for="floatingInput">Kode Barang</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control barang_name" id="barang_name" name="barang_name" placeholder="Barang">
                                    <label for="floatingInput">Nama Barang</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesifikasi" name="spesifikasi" placeholder="Spesifikasi">
                                    <label for="floatingInput">Spesifikasi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_mutasi" id="qty_mutasi" name="qty_mutasi" placeholder="Qty mutasi">
                                    <label for="floatingInput">Qty Mutasi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select unit_id_mutasi" name="unit_id_mutasi" id="unit_id_mutasi">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Mutasi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_asal" id="qty_asal" name="qty_asal" placeholder="Qty Asal">
                                    <label for="floatingInput">Stok Asal</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_konversi" id="qty_konversi" name="qty_konversi" placeholder="Qty Konversi">
                                    <label for="floatingInput">Qty Konversi</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select unit_id_konversi" name="unit_id_konversi" id="unit_id_konversi" disabled>
                                    <option value=""></option>
                                    <?php foreach ($dataSatuan as $d): ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['kode_satuan'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Konversi</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_hasil_mutasi" id="qty_hasil_mutasi" name="qty_hasil_mutasi" placeholder="Qty Hasil mutasi">
                                    <label for="floatingInput">Hasil mutasi</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideMutasiModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitMutasi">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var listStockInventori = [];
    var listStockSelected = [];
    $('.form-fifo').hide();
    <?php if (!empty($mutasiDetail)) : ?>
        listStockSelected = <?= json_encode($mutasiDetail) ?>;
        drawTableSelectedItem(listStockSelected);
    <?php else: ?>
        changeStatus();
    <?php endif; ?>

    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    var table = $('.table-inventori').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [12, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('mutasi/all-stock-list'); ?>",
            type: "GET",
            data: function(data) {
                data.spesifikasi_id = $("#spesifikasi_id").val();
                data.divisi_id = $("#divisi_asal_id").val();
                data.warehouse_id = $("#warehouse_asal_id").val();
                data.dateStart = $("#dateStart").val();
                data.dateEnd = $("#dateEnd").val();
                data.type_barang = $("#type_barang").val();
                data.search = $("#search").val();
            },
            dataSrc: function(json) {
                // simpan data hasil request ke variabel global
                window.listStockInventori = json.data;
                // kembalikan array data agar DataTables bisa menampilkannya
                return json.data;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-left",
                orderable: false
            },
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: function(data, type, row) {
                    return `<input type="checkbox" class="row-check child" value="${row.id}">`;
                }
            },
            {
                data: "divisi",
                className: "text-left"
            },
            {
                data: "warehouse_name",
                className: "text-left"
            },
            {
                data: "reference_type",
                className: "text-left"
            },
            {
                data: "supplier_name",
                className: "text-left"
            },
            {
                data: "kode_barang",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left",
            },
            {
                data: "spesifikasi",
                className: "text-left"
            },
            {
                data: "bc_detail",
                className: "text-left"
            },
            {
                data: "po_no",
                className: "text-left"
            },
            {
                data: "po_date",
                className: "text-left"
            },
            {
                data: "lpb_date",
                className: "text-left"
            },
            {
                data: "reference_no",
                className: "text-left"
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data);
                }
            },
            {
                data: "kode_satuan",
                className: "text-left"
            },
        ],
        "drawCallback": function(settings) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
            if (typePengambilanStock == "FIFO") {
                // Hide form check
                $('.child').hide();
            } else {
                $('.child').show();
            }
        },
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
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

    $("#dateStart,#dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('.tanggal').change(function(e) {
        e.preventDefault();
        changeStatus();
    });

    $('#dateStart,#dateEnd,#type_barang,#spesifikasi_id').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#btnDetailStockModal').click(function(e) {
        e.preventDefault();
        var divisiAsalId = $('#divisi_asal_id option:selected').val();
        var warehouseAsalId = $('#warehouse_asal_id option:selected').val();

        if (divisiAsalId == "") {
            Swal.fire({
                icon: 'error',
                title: "Pilih departemen asal dahulu, sebelum buka data inventori",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else if (warehouseAsalId == "") {
            Swal.fire({
                icon: 'error',
                title: "Pilih warehouse asal dahulu, sebelum buka data inventori",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            // reset tabel stok
            table.ajax.reload();
            var divisiAsal = $('#divisi_asal_id option:selected').text();
            var warehouseAsal = $('#warehouse_asal_id option:selected').text();
            $('#cari_stock_title')
                .text('Departemen ' + divisiAsal + ", Warehouse " + warehouseAsal)
                .addClass('text-danger');
            $('#type_pengambilan_stock').val("PABEAN").change();
            $('#detailStockModal').modal('show');
        }
    });

    $('#btnHideDetailStock').click(function(e) {
        e.preventDefault();
        $('#detailStockModal').modal('hide');
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
        $('#spesifikasi_id').val(null).change();
    });

    $('#warehouse_tujuan_id').select2({
        placeholder: "Pilih Warehouse Tujuan",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {});


    $('#warehouse_asal_id').select2({
        placeholder: "Pilih Warehouse Asal",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        listStockInventori = [];
        listStockSelected = [];
        getListWarehouseTujuan();
        drawTableSelectedItem(listStockSelected);
    });

    $('#divisi_asal_id').select2({
        placeholder: "Pilih Departemen Asal",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        getListWarehouseAsal()
        // RESET SEMUA LIST
        listStockInventori = [];
        listStockSelected = [];
        drawTableSelectedItem(listStockSelected);
    });

    $('#divisi_tujuan_id').select2({
        placeholder: "Pilih Departemen Tujuan",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        // CARI WAREHOUSE TUJUAN
        getListWarehouseTujuan();
    });


    $('#unit_id_mutasi').select2({
        placeholder: "Pilih Satuan Mutasi",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#detailMutasiModal')
    });

    $('#unit_id_konversi').select2({
        placeholder: "Pilih Satuan Konversi",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#detailMutasiModal')
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true,
    });

    $('#spesifikasi_id').select2({
        placeholder: "Cari Kode / Nama Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#detailStockModal'),
        ajax: {
            url: '<?= base_url("barang/dropdown/type-server-inventori") ?>',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    type_barang: $('#type_barang option:selected').val()
                };
            },
            processResults: function(data) {
                // Pastikan server mengembalikan data dengan struktur yang lengkap
                return {
                    results: $.map(data.results, function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                        };
                    })
                };
            },
            cache: false
        },
        minimumInputLength: 1
    });

    $('#btnHideMutasiModal').click(function(e) {
        e.preventDefault();
        $('#detailMutasiModal').modal('hide');
    });



    $("#type_barang,#divisi_asal_id,#divisi_tujuan_id,#warehouse_asal_id,#warehouse_tujuan_id,#spesifikasi_id,#bc_id,#unit_id_mutasi,#unit_id_konversi,#type_pengambilan_stock")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

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
            return parseFloat($(this).val());
        }).get();
        if (dataIds.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Checklist inventori yang ingin di mutasikan',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var id_selected = getIDListDataSelected();
            $.each(listStockInventori, function(i, v) {
                var currentID = Number(v.id);
                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStockSelected, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;

                    if (!isIDSelected) {
                        listStockInventori[i].mutasi = {
                            qty_mutasi: 0,
                            unit_id_mutasi: null,
                            unit_name_mutasi: "",
                            qty_konversi: 0,
                            unit_id_konversi: v.unit_id,
                            unit_name_konversi: v.kode_satuan,
                        }
                        listStockSelected.push(listStockInventori[i]);
                    }
                }
            });
        }
        drawTableSelectedItem(listStockSelected);
        // Tutup Modal Stok
        $('#detailStockModal').modal('hide');
    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var qtyMutasiFifo = parseFloat(destroyFormatRupiah($('#qty_mutasi_fifo').val()));
        var spesifikasiId = $(".spesifikasi_id option:selected").val();

        if (listStockInventori.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Inventori Kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (isNaN(qtyMutasiFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan : Qty Mutasi Keluar Wajib Diisi',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                reverseButtons: true,
                confirmButtonText: 'Oke',
            });
            return;
        } else if (spesifikasiId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Kode & Nama Barang Wajib Diisi',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var totalStokTotal = 0;
            $.each(listStockInventori, function(i, v) {
                totalStokTotal += parseFloat(v.qty_diterima);
            });

            if (qtyMutasiFifo > totalStokTotal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan : Stok barang tidak cukup !',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    reverseButtons: true,
                    confirmButtonText: 'Oke',
                });
                return;
            } else {
                $.each(listStockInventori, function(i, v) {
                    var currentID = Number(v.id);
                    if ($.inArray(currentID, dataIds) == -1) {
                        var isIDSelected = $.grep(listStockSelected, function(item) {
                            return item.id == Number(currentID);
                        }).length > 0;
                        if (!isIDSelected && qtyMutasiFifo != 0 && parseFloat(listStockInventori[i].qty_diterima) != 0) {
                            var mutasiQty = Math.min(qtyMutasiFifo, parseFloat(listStockInventori[i].qty_diterima));
                            var hasilMutasi = v.qty_diterima - mutasiQty;

                            listStockInventori[i].mutasi = {
                                qty_mutasi: parseFloat(mutasiQty),
                                unit_id_mutasi: v.unit_id,
                                unit_name_mutasi: v.kode_satuan,
                                qty_konversi: parseFloat(mutasiQty).toFixed(2),
                                unit_id_konversi: v.unit_id,
                                unit_name_konversi: v.kode_satuan,
                                hasil_mutasi: parseFloat(hasilMutasi).toFixed(2)
                            }

                            listStockSelected.push(listStockInventori[i]);
                            qtyMutasiFifo = qtyMutasiFifo - mutasiQty;
                        }
                    }
                });
            }
        }
        drawTableSelectedItem(listStockSelected);
        // Tutup Modal Stok
        $('#detailStockModal').modal('hide');
    }

    // VALIDATOR
    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true,
            },
            no_mutasi: {
                required: true
            },
            divisi_asal_id: {
                required: true
            },
            warehouse_asal_id: {
                required: true
            },
            divisi_tujuan_id: {
                required: true
            },
            warehouse_tujuan_id: {
                required: true,
            },
            type_pengambilan_stock: {
                required: true
            }
        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi",
            },
            no_mutasi: {
                required: "No mutasi wajib diisi"
            },
            divisi_asal_id: {
                required: "Departemen asal wajib diisi"
            },
            warehouse_asal_id: {
                required: "Warehouse asal wajib diisi"
            },
            divisi_tujuan_id: {
                required: "Departemen tujuan wajib diisi"
            },
            warehouse_tujuan_id: {
                required: "Warehouse tujuan wajib diisi",
            },
            type_pengambilan_stock: {
                required: "Tipe ambil stok wajib diisi"
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

    var validatorUpdateStock = $(".update-form-mutasi").validate({
        rules: {
            qty_mutasi: {
                required: true
            },
            unit_id_mutasi: {
                required: true
            },
            qty_konversi: {
                required: true
            },
            qty_hasil_mutasi: {
                required: true
            },
        },
        messages: {
            qty_mutasi: {
                required: "Qty mutasi wajib diisi"
            },
            unit_id_mutasi: {
                required: "Satuan mutasi wajib diisi"
            },
            qty_konversi: {
                required: "Qty konversi wajib diisi"
            },
            qty_hasil_mutasi: {
                required: "Qty hasil mutasi wajib diisi"
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


    $('.btn-submit-parent').click(function() {
        if (listStockSelected.length == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Barang yang akan dimutasi tidak boleh kosong !',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Ok'
            });
        } else {
            if ($('.create-form').valid()) {
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
                        let url = id == '' ? '<?= base_url("mutasi/save"); ?>' : '<?= base_url("mutasi/update"); ?>';
                        data.append('listMutasi', JSON.stringify(listStockSelected));
                        $.ajax({
                            url: url,
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
                                        window.location.href = "<?= base_url("mutasi"); ?>";
                                    }
                                });
                            },
                        });
                    }
                });

            }
        }
    });

    $('#btnSubmitMutasi').click(function(e) {
        e.preventDefault();
        if ($('.update-form-mutasi').valid()) {
            var qty_hasil_mutasi = destroyFormatRupiah($('#qty_hasil_mutasi').val());
            if (qty_hasil_mutasi < 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Qty hasil mutasi menghasilkan nilai minus  !",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                var id_stock_detail = $('#id_stock_detail').val();
                var qty_mutasi = parseFloat(destroyFormatRupiah($('#qty_mutasi').val()));
                var unit_id_mutasi = $('#unit_id_mutasi option:selected').val();
                var unit_name_mutasi = $('#unit_id_mutasi option:selected').text().trim();
                var qty_konversi = parseFloat(destroyFormatRupiah($('#qty_konversi').val()));
                var hasil_mutasi = parseFloat(destroyFormatRupiah($('#qty_hasil_mutasi').val()));

                var index = null;
                for (let i = 0; i < listStockSelected.length; i++) {
                    if (listStockSelected[i].id == id_stock_detail) {
                        index = i;
                    }
                }

                listStockSelected[index].mutasi.qty_mutasi = qty_mutasi;
                listStockSelected[index].mutasi.unit_id_mutasi = unit_id_mutasi;
                listStockSelected[index].mutasi.unit_name_mutasi = unit_name_mutasi;
                listStockSelected[index].mutasi.qty_konversi = qty_konversi;
                listStockSelected[index].mutasi.hasil_mutasi = hasil_mutasi;
                drawTableSelectedItem(listStockSelected);
                $('#detailMutasiModal').modal('hide');
            }
        }
    })


    function remove(id) {
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

    $('#qty_mutasi').keyup(function(e) {
        hitungHasilMutasi();
    });

    function hitungHasilMutasi() {
        var qty_mutasi = destroyFormatRupiah($('#qty_mutasi').val());
        var konversi = parseFloat($('#unit_id_mutasi option:selected').data('konversi_satuan'));
        var qty_asal = destroyFormatRupiah($('#qty_asal').val());
        var qty_konversi = 0;
        var qty_hasil_mutasi = 0;

        qty_konversi = qty_mutasi * konversi;
        qty_hasil_mutasi = qty_asal - qty_konversi;
        qty_konversi = parseFloat(qty_konversi).toFixed(2);
        qty_hasil_mutasi = parseFloat(qty_hasil_mutasi).toFixed(2);
        $('#qty_hasil_mutasi').val(greatFormatRupiah(qty_hasil_mutasi));
        $('#qty_konversi').val(greatFormatRupiah(qty_konversi));
    }

    function detail(id) {
        resetFormDetail();
        var item = null;
        for (let i = 0; i < listStockSelected.length; i++) {
            if (listStockSelected[i].id == id) {
                item = listStockSelected[i];
            }
        }
        if (item == '') {
            Swal.fire({
                icon: 'error',
                title: "Detail stok tidak ada (Kesalahan sistem)",
                confirmButtonColor: '#4e73df',
            });
            return;
        } else {
            $.ajax({
                url: "<?= base_url("mutasi/list-satuan-konversi"); ?>",
                data: {
                    id: id
                },
                method: "GET",
                success: function(response) {
                    if (response.status) {
                        var satuanmutasiArr = response.data;
                        $('#id_stock_detail').val(item.id);
                        $('#kode_barang').val(item.kode_barang);
                        $('#barang_name').val(item.barang_name);
                        $('#spesifikasi').val(item.spesifikasi);
                        $('#qty_mutasi').val(greatFormatRupiah(item.mutasi.qty_mutasi));
                        $('#unit_id_mutasi').val(item.mutasi.unit_id_mutasi).change();
                        $('#qty_konversi').val(greatFormatRupiah(item.mutasi.qty_konversi));
                        $('#unit_id_konversi').val(item.mutasi.unit_id_konversi).change();
                        $('#qty_hasil_mutasi').val(greatFormatRupiah(item.mutasi.hasil_mutasi));
                        $('#operasi_mutasi_detail').val(item.mutasi.operasi_mutasi_detail).change();
                        $('#qty_asal').val(greatFormatRupiah(item.qty_diterima));

                        // append select
                        dropdownUnitMutasi(satuanmutasiArr);
                        $('#unit_id_mutasi').val(item.mutasi.unit_id_konversi).change();

                        $('#detailMutasiModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            confirmButtonColor: '#4e73df',
                            cancelButtonColor: '#d33',
                            reverseButtons: true,
                            confirmButtonText: 'Oke',
                        });
                        return;
                    }
                },
            });
        }
    }

    function resetFormDetail() {
        $('#id_stock_detail').val(null);
        $('#kode_barang').val(null);
        $('#barang_name').val(null);
        $('#spesifikasi').val(null);
        $('#qty_mutasi').val(null);
        $('#unit_id_mutasi').val(null).change();
        $('#qty_konversi').val(null);
        $('#unit_id_konversi').val(null).change();
        $('#qty_hasil_mutasi').val(null);
        $('#operasi_mutasi_detail').val(null).change();
        $('#qty_asal').val(null);
    }


    function dropdownUnitMutasi(satuanArr) {
        $("#unit_id_mutasi").empty()
        $("#unit_id_mutasi").append(`<option value=""></option>`)
        satuanArr.forEach(function(item) {
            $("#unit_id_mutasi").append(`<option data-konversi_satuan="${item.konversi_satuan}" value="${item.id}">${item.kode_satuan}</option>`)
        });
    }

    function getListWarehouseAsal() {
        // GET LIST WAREHOUSE ASAL
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_asal_id").empty()
                $(".warehouse_asal_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_asal_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    function getListWarehouseTujuan() {
        // GET LIST WAREHOUSE TUJUAN
        $.ajax({
            url: `<?= base_url('mutasi/warehouse'); ?>`,
            method: "GET",
            data: {
                divisi_id: $(".divisi_tujuan_id option:selected").val(),
                warehouse_asal_id: $(".warehouse_asal_id option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                $(".warehouse_tujuan_id").empty()
                $(".warehouse_tujuan_id").append(`<option value=""></option>`)
                res.data.forEach(function(item) {
                    $(".warehouse_tujuan_id").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })
            }
        });
    }

    function changeStatus() {
        let value = document.getElementById('auto_generate').checked ? true : false;
        let tanggal = $('#tanggal').val();
        if (value) {
            $("#no_mutasi").attr("readonly", true);
            $.ajax({
                url: `<?= base_url("mutasi/get-mutasi-no"); ?>`,
                data: {
                    tanggal: tanggal
                },
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $("#no_mutasi").val(res.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                        $("#no_mutasi").attr("readonly", false);
                        $("#auto_generate").prop("checked", false);
                        $("#no_mutasi").val("");
                    }
                }
            })
        } else {
            $("#no_mutasi").attr("readonly", false);
            $("#no_mutasi").val("");
        }
    }

    function drawTableSelectedItem(data) {
        const table = $('#selectedItemTable');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (data.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="17" >').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            $.each(data, function(i, v) {
                var newRow = $('<tr>');
                newRow.append($('<td>').html(
                    `
                   ${no++} 
                `
                ));
                newRow.append($('<td>').text(v.reference_type));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name));
                newRow.append($('<td>').text(v.spesifikasi));
                newRow.append($('<td>').text(v.bc_detail));
                newRow.append($('<td>').text(v.po_no));
                newRow.append($('<td>').text(v.po_date));
                newRow.append($('<td>').text(v.lpb_date));
                newRow.append($('<td>').text(v.reference_no));
                newRow.append($('<td>').text(greatFormatRupiah(v.qty_diterima)));
                newRow.append($('<td>').text(greatFormatRupiah(v.mutasi.qty_mutasi)));
                newRow.append($('<td>').text(v.mutasi.unit_name_mutasi));
                newRow.append($('<td>').text(greatFormatRupiah(v.mutasi.qty_konversi)));
                newRow.append($('<td>').text(v.mutasi.unit_name_konversi));
                newRow.append($('<td >').html(
                    `
                    <button type="button" class="btn btn-warning posting-spp mr-1" onclick="detail('${v.id}')">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="btn btn-danger" onclick="remove('${v.id}')">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>
                `
                ));
                table.find('tbody').append(newRow);
            });
        }

    }

    function getIDListDataSelected() {
        var id_selected = [];
        $.each(listStockSelected, function(i, v) {
            id_selected.push(v.id);
        })
        return id_selected;
    }

    $('.posting-mutasi').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Mutasi ?',
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
                    url: "<?= base_url("mutasi/posting"); ?>",
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
                                window.location.href = "<?= base_url("mutasi"); ?>";
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
            title: 'Hapus Mutasi ?',
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
                    url: "<?= base_url("mutasi/delete"); ?>",
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
                                window.location.href = "<?= base_url('mutasi') ?>"
                            });
                        }
                    },
                });
            }
        })
    });
</script>



<?= $this->endSection(); ?>