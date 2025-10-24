<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="section-header">
        <h1><?= empty($adjusment) ? "Tambah Stok Adjusment" : "Update Stok Adjusment" ?></h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-adjusment"); ?>">
                Kembali
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
                                    <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> autocomplete="one-time-code" class="form-control input-picker tanggal" id="tanggal" name="tanggal" placeholder="Tanggal Dibuat" value="<?= date('d/m/Y', strtotime(!empty($adjusment) ? $adjusment['tanggal'] : $tanggal)); ?>">
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
                                    <input autocomplete="one-time-code" <?= !empty($adjusment) ? ($adjusment['status_posting'] == 1 ? 'readonly' : '')  : ''; ?> value="<?= !empty($adjusment) ? $adjusment['no_adjusment'] : ""; ?>" type="text" class="form-control no_adjusment" id="no_adjusment" name="no_adjusment" placeholder="No. Adjusment">
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
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select divisi_id" id="divisi_id" name="divisi_id">
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
                            <select <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> class="form-select tipe_adjusment" id="tipe_adjusment" name="tipe_adjusment">
                                <option value=""></option>
                                <?php foreach ($tipeAdjusment as $t) : ?>
                                    <option <?= !empty($adjusment) ? ($adjusment['tipe_adjusment'] == $t['id'] ? 'selected' : '') : '' ?> value="<?= $t['id'] ?>">
                                        <?= $t['value']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput" style="z-index: 1;">Tipe Adjusment</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input <?= !empty($adjusment) ? ($adjusment['status_posting'] ? 'disabled' : '') : '' ?> placeholder="Keterangan" value="<?= !empty($adjusment) ? $adjusment['keterangan'] : '' ?>" class="form-control keterangan" id="keterangan" name="keterangan" />
                            <label for="floatingInput" style="z-index: 1;">Keterangan (Opsional)</label>
                        </div>
                    </div>
                </div>

            </form>

            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label font-weight-bold modal-sub-title">List Adjusment Barang</label>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-show-detail btn-add btn-block float-right" type="button" id="btnDetailStockModal">
                            <i class="fa-solid fa-magnifying-glass"></i> Inventori
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-adjusment" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Warehouse</th>
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
                                <th>Operasi</th>
                                <th>Qty Adjusment</th>
                                <th>Satuan Adjusment</th>
                                <th>Qty Konversi</th>
                                <th>Satuan Konversi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="19">Tidak Ada Data</td>
                            </tr>
                        </tfoot>
                    </table>
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
                                <div class="form-floating mb-3" style="height: 50px;">
                                    <select class="form-select operasi_fifo" id="operasi_fifo" name="operasi_fifo">
                                        <option value=""></option>
                                        <option value="PLUS">PENAMBAHAN STOK</option>
                                        <option value="MINUS">PENGURANGAN STOK</option>
                                    </select>
                                    <label for="floatingInput" style="z-index: 1;">Operasi</label>
                                </div>
                            </div>
                            <div class="col-md-4 form-fifo">
                                <div class="form-floating" style="height: 50px;">
                                    <input placeholder="Qty" oninput="this.value = greatFormatRupiah(this.value)" class="form-control qty_adjusment_fifo" id="qty_adjusment_fifo" name="qty_adjusment_fifo" />
                                    <label for="floatingInput" style="z-index: 1;">Qty Adjusment</label>
                                </div>
                            </div>
                        </div>

                        <label class="form-label font-weight-bold lable-title">Pilih Inventori Barang Yang Akan Di Adjusment</label>
                        <div class="row mt-3 justify-content-left">

                            <div class="col-sm-3 mb-2">
                                <div class="form-floating" style="height: 50px;">
                                    <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
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


<div class="modal detail-modal" id="detailAdjusmentModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Input Qty Adjusment</h5>
            </div>
            <form class="update-form-adjusment" role="form" method="POST">
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
                                    <input oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_adjusment" id="qty_adjusment" name="qty_adjusment" placeholder="Qty Adjusment">
                                    <label for="floatingInput">Qty Adjusment</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select unit_id_adjusment" name="unit_id_adjusment" id="unit_id_adjusment">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Satuan Adjusment</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select operasi_adjusment_detail" name="operasi_adjusment_detail" id="operasi_adjusment_detail">
                                    <option value=""></option>
                                    <option value="PLUS">PENAMBAHAN STOK</option>
                                    <option value="MINUS">PENGURANGAN STOK</option>
                                </select>
                                <label for="floatingInput" style="z-index: 1;">Tipe Adjusment</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                    <input readonly oninput="this.value = greatFormatRupiah(this.value)" autocomplete="one-time-code" type="text" class="form-control qty_hasil_adjusment" id="qty_hasil_adjusment" name="qty_hasil_adjusment" placeholder="Qty Hasil Adjusment">
                                    <label for="floatingInput">Hasil Adjusment</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-discard mr-2" id="btnHideAdjusmentModal">Kembali</button>
                    <button type="button" class="btn btn-submit-form" id="btnSubmitAdjusment">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    var listStockInventori = [];
    var listStock = [];
    var qtyTotal = 0;
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    $('.form-fifo').hide();

    <?php if (!empty($adjusment)) : ?>
        listStock = <?= json_encode($dataListBarang) ?>;
        drawTable();
    <?php else: ?>
        changeStatus();
    <?php endif; ?>

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
            url: "<?= base_url('stock-adjusment/all-stock-list'); ?>",
            type: "GET",
            data: function(data) {
                data.spesifikasi_id = $("#spesifikasi_id").val();
                data.divisi_id = $("#divisi_id").val();
                data.warehouse_id = $("#warehouse_id").val();
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
                data: "type_bc",
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

    // FILTER
    $('#divisi_id,#warehouse_id,#dateStart,#dateEnd,#spesifikasi_id,#type_barang').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // TANGGAL
    $('#tanggal').change(function(e) {
        e.preventDefault();
        changeStatus();
    })

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        dropdownWarehouse();
        table.ajax.reload();
        // Reset ListStok
        listStock = [];
        drawTable();

    });

    $('#type_pengambilan_stock').select2({
        placeholder: "Pilih Tipe Ambil Stok",
        theme: "bootstrap-5",
        dropdownParent: $('#detailStockModal')
    }).change(function() {
        // FIFO
        if ($(this).val() == "FIFO") {
            $('.form-fifo').show();
            // Hide form check
            $('.child').hide();
        } else {
            $('.form-fifo').hide();
            $('.child').show();
        }
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#detailStockModal')
    }).change(function() {

    });

    $('#tipe_adjusment').select2({
        placeholder: "Pilih Tipe Adjusment",
        theme: "bootstrap-5",
        allowClear: true,
    }).change(function() {});

    $('#operasi_adjusment_detail').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#detailAdjusmentModal')
    }).change(function() {});

    $('#operasi_fifo').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#detailStockModal')
    }).change(function() {});


    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true,
        dropdownParent: $('#detailStockModal')
    });

    $('#unit_id_adjusment').select2({
        placeholder: "Pilih Satuan Adjusment",
        theme: "bootstrap-5",
        allowClear: false,
        dropdownParent: $('#detailAdjusmentModal')
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

    $('#operasi').select2({
        placeholder: "Pilih Operasi",
        theme: "bootstrap-5",
        allowClear: true
    });

    //CSS SELECT2 FLOATING LABEL
    $('#unit_id_adjusment,#operasi_adjusment_detail,#unit_id_konversi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('#unit_id_adjusment,#operasi_adjusment_detail,#unit_id_konversi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#unit_id_adjusment,#operasi_adjusment_detail,#unit_id_konversi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#unit_id_adjusment,#operasi_adjusment_detail,#unit_id_konversi')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $("#dateStart,#dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $("#tanggal").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    // VALIDATOR HEADER
    var validator = $(".create-form").validate({
        rules: {
            tanggal: {
                required: true
            },
            no_adjusment: {
                required: true
            },
            divisi_id: {
                required: true
            },
            tipe_adjusment: {
                required: true
            },
        },
        messages: {
            tanggal: {
                required: "Tanggal wajib diisi"
            },
            no_adjusment: {
                required: "No adjusment wajib diisi"
            },
            divisi_id: {
                required: "Departemen wajib diisi"
            },
            tipe_adjusment: {
                required: "Tipe adjusment wajib diisi"
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

    // UNTUK UPDATE STOCK
    var validatorUpdateStock = $(".update-form-adjusment").validate({
        rules: {
            qty_adjusment: {
                required: true
            },
            unit_id_adjusment: {
                required: true
            },
            qty_konversi: {
                required: true
            },
            qty_hasil_adjusment: {
                required: true
            },
        },
        messages: {
            qty_adjusment: {
                required: "Qty adjusment wajib diisi"
            },
            unit_id_adjusment: {
                required: "Satuan adjusment wajib diisi"
            },
            qty_konversi: {
                required: "Qty konversi wajib diisi"
            },
            qty_hasil_adjusment: {
                required: "Qty hasil adjusment wajib diisi"
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
                title: 'List adjusment barang kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            })
        } else {
            if ($('.create-form').valid()) {
                var id = $('.id').val();
                var url = id != '' ? "<?= base_url("stock-adjusment/update"); ?>" : "<?= base_url("stock-adjusment/save"); ?>";
                var formData = new FormData(document.querySelector('.create-form'));
                formData.append("listBarang", JSON.stringify(listStock));
                // UPDATE
                $.ajax({
                    url: url,
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
                                    window.location.href = "<?= base_url("stock-adjusment"); ?>";
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
    });

    $('#btnDetailStockModal').click(function(e) {
        e.preventDefault();
        var divisiId = $('#divisi_id option:selected').val();
        if (divisiId == "") {
            Swal.fire({
                icon: 'error',
                title: "Pilih departemen dahulu, sebelum buka data inventori",
                confirmButtonColor: '#4e73df',
            });
        } else {
            table.ajax.reload(); // reset from server
            var divisi = $('#divisi_id option:selected').text();
            $('#cari_stock_title').text('Pilih Tipe Ambil Stok, Departemen ' + divisi);
            $('#type_pengambilan_stock').val("PABEAN").change();
            $('#detailStockModal').modal('show');
        }
    });

    $('#btnHideDetailStock').click(function(e) {
        e.preventDefault();
        $('#detailStockModal').modal('hide');
    });

    // PILIH ADJUSMENT
    $('#select-item-btn').click(function() {
        var typePengambilanStock = $('#type_pengambilan_stock option:selected').val();
        if (typePengambilanStock == "FIFO") {
            insertListFifo();
        } else {
            insertListPabean();
        }
    });

    // Operasi Adjusment
    $('#qty_adjusment').keyup(function(e) {
        hitungHasilAdjusment();
    });

    $('#unit_id_adjusment,#operasi_adjusment_detail').change(function(e) {
        e.preventDefault();
        hitungHasilAdjusment();
    });

    $('#btnSubmitAdjusment').click(function(e) {
        e.preventDefault();
        if ($('.update-form-adjusment').valid()) {
            var qty_hasil_adjusment = destroyFormatRupiah($('#qty_hasil_adjusment').val());
            if (qty_hasil_adjusment < 0) {
                Swal.fire({
                    icon: 'error',
                    title: "Qty hasil adjusment menghasilkan nilai minus  !",
                    confirmButtonColor: '#4e73df',
                });
                return;
            } else {
                var id_stock_detail = $('#id_stock_detail').val();
                var operasi_adjusment_detail = $('#operasi_adjusment_detail option:selected').val();
                var qty_adjusment = parseFloat(destroyFormatRupiah($('#qty_adjusment').val()));
                var unit_id_adjusment = $('#unit_id_adjusment option:selected').val();
                var unit_name_adjusment = $('#unit_id_adjusment option:selected').text().trim();
                var qty_konversi = parseFloat(destroyFormatRupiah($('#qty_konversi').val()));
                var hasil_adjusment = parseFloat(destroyFormatRupiah($('#qty_hasil_adjusment').val()));

                var index = null;
                for (let i = 0; i < listStock.length; i++) {
                    if (listStock[i].id == id_stock_detail) {
                        index = i;
                    }
                }

                listStock[index].adjusment.operasi_adjusment_detail = operasi_adjusment_detail;
                listStock[index].adjusment.qty_adjusment = qty_adjusment;
                listStock[index].adjusment.unit_id_adjusment = unit_id_adjusment;
                listStock[index].adjusment.unit_name_adjusment = unit_name_adjusment;
                listStock[index].adjusment.qty_konversi = qty_konversi;
                listStock[index].adjusment.hasil_adjusment = hasil_adjusment;
                drawTable();
                $('#detailAdjusmentModal').modal('hide');
            }
        }
    })

    function hitungHasilAdjusment() {
        var operasi_adjusment_detail = $('#operasi_adjusment_detail option:selected').val();
        var qty_adjusment = destroyFormatRupiah($('#qty_adjusment').val());
        var konversi = parseFloat($('#unit_id_adjusment option:selected').data('konversi_satuan'));
        var qty_asal = destroyFormatRupiah($('#qty_asal').val());
        var qty_konversi = 0;
        var qty_hasil_adjusment = 0;

        if (operasi_adjusment_detail != '') {
            qty_konversi = (qty_adjusment * konversi);

            if (operasi_adjusment_detail == "PLUS") {
                // PLUS
                qty_hasil_adjusment = qty_konversi + qty_asal;
            } else {
                // MINUS
                qty_hasil_adjusment = qty_asal - qty_konversi;
            }

            qty_konversi = parseFloat(qty_konversi);
            qty_hasil_adjusment = parseFloat(qty_hasil_adjusment);
            $('#qty_hasil_adjusment').val(greatFormatRupiah(qty_hasil_adjusment));
            $('#qty_konversi').val(greatFormatRupiah(qty_konversi));
        } else {
            $('#qty_hasil_adjusment').val(null);
            $('#qty_konversi').val(null);
        }
    }

    $('.posting-adjusment').click(function() {
        Swal.fire({
            icon: 'question',
            title: 'Posting Adjusment ?',
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
                                window.location.href = "<?= base_url("stock-adjusment"); ?>";
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
            cancelButtonText: 'Kembali',
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
            return parseFloat($(this).val());
        }).get();
        if (dataIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Checklist inventori yang ingin di adjusment',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            })
        } else {
            var id_selected = getIDListDataSelected();
            $.each(listStockInventori, function(i, v) {
                var currentID = Number(v.id);
                if ($.inArray(currentID, dataIds) !== -1) {
                    var isIDSelected = $.grep(listStock, function(item) {
                        return item.id == Number(currentID);
                    }).length > 0;

                    if (!isIDSelected) {
                        listStockInventori[i].adjusment = {
                            operasi_adjusment_detail: "",
                            qty_adjusment: 0,
                            unit_id_adjusment: null,
                            unit_name_adjusment: "",
                            qty_konversi: 0,
                            unit_id_konversi: v.unit_id,
                            unit_name_konversi: v.kode_satuan,
                            hasil_adjusment: 0
                        }
                        listStock.push(listStockInventori[i]);
                    }
                }
            });

            drawTable(listStock);
            // Tutup Modal Stok
            $('#detailStockModal').modal('hide');
        }

    }

    function insertListFifo() {
        var dataIds = getIDListDataSelected();
        var operasiFifo = $('#operasi_fifo option:selected').val();
        var qtyAdjusmentFifo = parseFloat(destroyFormatRupiah($('#qty_adjusment_fifo').val()));
        var spesifikasiId = $(".spesifikasi_id option:selected").val();

        if (listStockInventori.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Inventori Kosong',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (operasiFifo == "" || isNaN(qtyAdjusmentFifo)) {
            Swal.fire({
                icon: 'error',
                title: 'Operasi dan Qty Adjusment Fifo Wajib Diisi',
                confirmButtonColor: '#4e73df',
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

            if (operasiFifo == "MINUS") {
                if (qtyAdjusmentFifo > totalStokTotal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan : Operasi FIFO akan menghasilkan nilai minus',
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Oke',
                    })
                } else {
                    $.each(listStockInventori, function(i, v) {
                        var currentID = Number(v.id);
                        if ($.inArray(currentID, dataIds) == -1) {
                            var isIDSelected = $.grep(listStock, function(item) {
                                return item.id == Number(currentID);
                            }).length > 0;

                            if (!isIDSelected && qtyAdjusmentFifo != 0 && parseFloat(listStockInventori[i].qty_diterima) != 0) {
                                var adjusmentQty = Math.min(qtyAdjusmentFifo, parseFloat(listStockInventori[i].qty_diterima));
                                var hasilAdjusmnet = v.qty_diterima - adjusmentQty;

                                listStockInventori[i].adjusment = {
                                    operasi_adjusment_detail: "MINUS",
                                    qty_adjusment: parseFloat(adjusmentQty),
                                    unit_id_adjusment: v.unit_id,
                                    unit_name_adjusment: v.kode_satuan,
                                    qty_konversi: parseFloat(adjusmentQty),
                                    unit_id_konversi: v.unit_id,
                                    unit_name_konversi: v.kode_satuan,
                                    hasil_adjusment: parseFloat(hasilAdjusmnet)
                                }
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

                        if (!isIDSelected && qtyAdjusmentFifo != 0 && parseFloat(listStockInventori[i].qty_diterima) != 0) {
                            var adjusmentQty = Math.min(qtyAdjusmentFifo, parseFloat(listStockInventori[i].qty_diterima));
                            var hasilAdjusmnet = v.qty_diterima + adjusmentQty;

                            listStockInventori[i].adjusment = {
                                operasi_adjusment_detail: "PLUS",
                                qty_adjusment: parseFloat(adjusmentQty),
                                unit_id_adjusment: v.unit_id,
                                unit_name_adjusment: v.kode_satuan,
                                qty_konversi: parseFloat(adjusmentQty),
                                unit_id_konversi: v.unit_id,
                                unit_name_konversi: v.kode_satuan,
                                hasil_adjusment: parseFloat(hasilAdjusmnet)
                            }
                            listStock.push(listStockInventori[i]);

                            qtyAdjusmentFifo = qtyAdjusmentFifo - adjusmentQty;

                        }
                    }
                });
            }
            drawTable();
            $('#detailStockModal').modal('hide');

        }
    }

    function remove(id) {
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

    function detail(id) {
        resetFormDetail();
        var item = null;
        for (let i = 0; i < listStock.length; i++) {
            if (listStock[i].id == id) {
                item = listStock[i];
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
                url: "<?= base_url("stock-adjusment/list-satuan-konversi"); ?>",
                data: {
                    id: id
                },
                method: "GET",
                success: function(response) {
                    if (response.status) {
                        var satuanAdjusmentArr = response.data;
                        $('#id_stock_detail').val(item.id);
                        $('#kode_barang').val(item.kode_barang);
                        $('#barang_name').val(item.barang_name);
                        $('#spesifikasi').val(item.spesifikasi);
                        $('#qty_adjusment').val(greatFormatRupiah(item.adjusment.qty_adjusment));
                        $('#unit_id_adjusment').val(item.adjusment.unit_id_adjusment).change();
                        $('#qty_konversi').val(greatFormatRupiah(item.adjusment.qty_konversi));
                        $('#unit_id_konversi').val(item.adjusment.unit_id_konversi).change();
                        $('#qty_hasil_adjusment').val(greatFormatRupiah(item.adjusment.hasil_adjusment));
                        $('#operasi_adjusment_detail').val(item.adjusment.operasi_adjusment_detail).change();
                        $('#qty_asal').val(greatFormatRupiah(item.qty_diterima));

                        // append select
                        dropdownUnitAdjusment(satuanAdjusmentArr);
                        $('#unit_id_adjusment').val(item.adjusment.unit_id_konversi).change();

                        $('#detailAdjusmentModal').modal('show');
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
        $('#qty_adjusment').val(null);
        $('#unit_id_adjusment').val(null).change();
        $('#qty_konversi').val(null);
        $('#unit_id_konversi').val(null).change();
        $('#qty_hasil_adjusment').val(null);
        $('#operasi_adjusment_detail').val(null).change();
        $('#qty_asal').val(null);
    }

    $('#btnHideAdjusmentModal').click(function(e) {
        e.preventDefault();
        $('#detailAdjusmentModal').modal('hide');
    });

    function drawTable() {
        const table = $('.table-adjusment');
        table.find('tbody').empty();
        table.find('tfoot').empty();
        var no = 1;
        if (listStock.length == 0) {
            var newRow = $('<tr>');
            newRow.append($('<td  colspan="19" >').text("Tidak Ada Data"));
            table.find('tfoot').append(newRow);
        } else {
            var no = 1;
            $.each(listStock, function(i, v) {
                var newRow = $('<tr style="color:whitesmoke;">');
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(v.warehouse_name));
                newRow.append($('<td>').text(v.reference_type));
                newRow.append($('<td>').text(v.supplier_name));
                newRow.append($('<td>').text(v.kode_barang));
                newRow.append($('<td>').text(v.barang_name));
                newRow.append($('<td>').text(v.spesifikasi));
                newRow.append($('<td>').text(v.type_bc));
                newRow.append($('<td>').text(v.po_no));
                newRow.append($('<td>').text(v.po_date));
                newRow.append($('<td>').text(v.lpb_date));
                newRow.append($('<td>').text(v.reference_no));
                newRow.append($('<td>').text(greatFormatRupiah(v.qty_diterima)));
                newRow.append($('<td>').text(

                    `${v.adjusment.operasi_adjusment_detail == "" ? "" : (v.adjusment.operasi_adjusment_detail == "PLUS" ? "( + )" : "( - )")}`
                ));
                newRow.append($('<td>').text(greatFormatRupiah(v.adjusment.qty_adjusment)));
                newRow.append($('<td>').text(v.adjusment.unit_name_adjusment));
                newRow.append($('<td>').text(greatFormatRupiah(v.adjusment.qty_konversi)));
                newRow.append($('<td>').text(v.adjusment.unit_name_konversi));


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

    function dropdownWarehouse() {
        $.ajax({
            url: `<?= base_url('stock-adjusment/warehouse'); ?>`,
            method: "GET",
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

    function dropdownUnitAdjusment(satuanArr) {
        $("#unit_id_adjusment").empty()
        $("#unit_id_adjusment").append(`<option value=""></option>`)
        satuanArr.forEach(function(item) {
            $("#unit_id_adjusment").append(`<option data-konversi_satuan="${item.konversi_satuan}" value="${item.id}">${item.kode_satuan}</option>`)
        });
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
                isAdjusment: true
            },
            dataType: "json",
            success: function(res) {
                // DRAW TABLE LIST INVENTORI BARANG
                listStockInventori = res.data
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
                    tanggal: $('#tanggal').val()
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