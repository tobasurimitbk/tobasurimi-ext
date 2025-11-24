<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Kartu Stok</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("stock-list"); ?>">
                Kembali
            </a>
            <button class="btn btn-discard float-right" type="button" id="btnExportKartuStock" style="background-color: #FFA426 !important;color: white !important;border: 0px solid !important;">
                Export
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form class="create-form">
                <?= csrf_field() ?>
                <input type="hidden" class="id_detail" name="id_detail" id="id_detail">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
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
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select barang_master_id" id="barang_master_id" name="barang_master_id">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Cari Master Barang</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating" style="height: 50px;">
                                <input value="01/<?= date('m/Y') ?>" placeholder="" class="form-control start_date" id="start_date" name="start_date" />
                                <label style="z-index: 1;" style="z-index: 1;">Tgl Awal</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating" style="height: 50px;">
                                <input value="<?= date('d/m/Y') ?>" placeholder="" class="form-control end_date" id="end_date" name="end_date" />
                                <label style="z-index: 1;" style="z-index: 1;">Tgl Akhir</label>
                            </div>
                            <div class="input-group-append" style="height:50px;">
                                <button disabled class="btn btn-secondary" type="button">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
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
                    <div class="col-md-3">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">
                                <option value=""></option>

                            </select>
                            <label for="floatingInput" style="z-index: 1;">Warehouse</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="form-floating" style="height: 50px;">
                                <input type="text" name="search_kartu" id="search_kartu" class="form-control search_kartu" placeholder="Cari Data">
                                <label style="z-index: 1;">Cari Data</label>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('divisi_id')">Dept</th>
                                <th onclick="changeSort('warehouse_id')">Warehouse</th>
                                <th onclick="changeSort('kode_barang')">Kode Barang</th>
                                <th onclick="changeSort('barang_master_id')">Barang</th>
                                <th onclick="changeSort('spesifikasi_id')">Spesifikasi</th>
                                <th>Saldo Awal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Saldo Akhir</th>
                                <th onclick="changeSort('unit_id')">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal detail-modal" id="detailPemasukkanModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 100rem !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Detail Pemasukan</h5>
            </div>
            <div class="modal-body">
                <div class="detail-form-component">
                    <div class="detail-form-layout">
                        <div class="row">
                            <div class="col-sm-12">
                                <table>
                                    <tr>
                                        <td>Kode Barang</td>
                                        <td>:</td>
                                        <td id="txt_kode_barang_masuk"></td>
                                        <td></td>
                                        <td>Dept / Warehouse</td>
                                        <td>:</td>
                                        <td id="txt_dept_masuk"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Barang</td>
                                        <td>:</td>
                                        <td id="txt_nama_barang_masuk"></td>
                                        <td></td>
                                        <td>Rentang Tanggal</td>
                                        <td>:</td>
                                        <td id="txt_date_range_masuk"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <button class="btn btn-discard float-right" type="button" id="btnExportPemasukkan" style="background-color: #FFA426 !important;color: white !important;border: 0px solid !important;">
                                                Export Pemasukkan
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>


                        <div class="row mt-3 justify-content-end">
                            <div class="col-sm-3 mb-2">
                                <input autocomplete="one-time-code" class="form-control search search_masuk form-out-search" id="search_masuk" placeholder="Search Data" value="" />

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-table-button-tts" style="margin-top: 5px;">
                                <div class="table-responsive">
                                    <table class="table table-responsive table-bordered nowrap table-hover-tobasurimi dataTable table-pemasukkan" id="dataTableMasuk" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Sumber</th>
                                                <th>Supplier / Vendor</th>
                                                <th>No Spp</th>
                                                <th>No Po</th>
                                                <th>Ref No</th>
                                                <th>Tgl Po</th>
                                                <th>Tgl Masuk</th>
                                                <th>Keterangan</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="9" class="text-right">GRAND TOTAL</th>
                                                <th class="text-left total_masuk_detail" id="total_masuk_detail"></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard mr-2" id="btnHidePemasukkan">Kembali</button>
            </div>
        </div>
    </div>
</div>

<div class="modal detail-modal" id="detailPengeluaranModal" tabindex="1">
    <div class="modal-dialog modal-xl" style="min-width: 100rem !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary">Detail Pengeluaran</h5>
            </div>
            <div class="modal-body">
                <div class="detail-form-component">
                    <div class="detail-form-layout">
                        <div class="row">
                            <div class="col-sm-12">
                                <table>
                                    <tr>
                                        <td>Kode Barang</td>
                                        <td>:</td>
                                        <td id="txt_kode_barang_keluar"></td>
                                        <td></td>
                                        <td>Dept / Warehouse</td>
                                        <td>:</td>
                                        <td id="txt_dept_keluar"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Barang</td>
                                        <td>:</td>
                                        <td id="txt_nama_barang_keluar"></td>
                                        <td></td>
                                        <td>Rentang Tanggal</td>
                                        <td>:</td>
                                        <td id="txt_date_range_keluar"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <button class="btn btn-discard float-right" type="button" id="btnExportPengeluaran" style="background-color: #FFA426 !important;color: white !important;border: 0px solid !important;">
                                                Export Pengeluaran
                                            </button>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>


                        <div class="row mt-3 justify-content-end">
                            <div class="col-sm-3 mb-2">
                                <input autocomplete="one-time-code" class="form-control search search_keluar form-out-search" id="search_keluar" placeholder="Search Data" value="" />

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-table-button-tts" style="margin-top: 5px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable table-keluar" id="dataTableKeluar" width="100%" cellspacing="0">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Tujuan Keluar</th>
                                                <th>Dept Tujuan</th>
                                                <th>Warehouse Tujuan</th>
                                                <th>Ref No</th>
                                                <th>Tgl Keluar</th>
                                                <th>Keterangan</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="body-table">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" class="text-right">GRAND TOTAL</th>
                                                <th class="text-left total_keluar_detail" id="total_keluar_detail"></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-discard mr-2" id="btnHidePengeluaran">Kembali</button>
            </div>
        </div>
    </div>
</div>

<script>
    var listStock = [];
    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    let sort = "stock_revamp.barang_master_id";
    let sortType = "desc";
    let stock_id = "";


    const table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [4, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/all-kartu-stock"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search_kartu").val();
                data.start_date = $("#start_date").val();
                data.end_date = $("#end_date").val();
                data.divisi_id = $("#divisi_id option:selected").val();
                data.warehouse_id = $("#warehouse_id option:selected").val();
                data.barang_master_id = $('#barang_master_id option:selected').val();
                data.type_barang = $('#type_barang option:selected').val();
                data.sort = sort;
                data.sortType = sortType;
            },

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
                data: "divisi",
                className: "text-left"
            },
            {
                data: "warehouse_name",
                className: "text-left"
            },
            {
                data: "kode_barang",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left"
            },
            {
                data: "spesifikasi",
                className: "text-left",
            },
            {
                data: "qty_awal",
                className: "text-left",
                render: function(data, type, row) {
                    let qty_awal = parseFloat(row.qty_awal).toFixed(2);
                    return `<b>${greatFormatRupiah(qty_awal)}</b>`;
                }
            },
            {
                data: "qty_masuk",
                className: "text-left",
                render: function(data, type, row) {
                    let id = row.id;
                    let qty_masuk = parseFloat(row.qty_masuk).toFixed(2);
                    return `<b><a onclick="detailMasuk('${id}')" href="#">${greatFormatRupiah(qty_masuk)}</a></b>`;
                }
            },
            {
                data: "qty_keluar",
                className: "text-left",
                render: function(data, type, row) {
                    let id = row.id;
                    let qty_keluar = parseFloat(row.qty_keluar).toFixed(2);
                    return `<b><a onclick="detailKeluar('${id}')" href="#">${greatFormatRupiah(qty_keluar)}</a></b>`;
                }
            },
            {
                data: "qty_akhir",
                className: "text-left",
                render: function(data, type, row) {
                    let qty_akhir = parseFloat(row.qty_akhir).toFixed(2);
                    return `<b>${greatFormatRupiah(qty_akhir)}</b>`;
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
        },

    });


    const tableMasuk = $('#dataTableMasuk').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [6, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/all-masuk-kartu-stock"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search_masuk").val();
                data.start_date = $("#start_date").val();
                data.end_date = $("#end_date").val();
                data.stock_id = stock_id;
                data.sort = sort;
                data.sortType = sortType;
            },

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
                data: "reference_type",
                className: "text-left"
            },
            {
                data: "supplier_name",
                className: "text-left"
            },
            {
                data: "spp_no",
                className: "text-left",
            },
            {
                data: "po_no",
                className: "text-left"
            },
            {
                data: "reference_no",
                className: "text-left"
            },
            {
                data: "po_date",
                className: "text-left",
            },
            {
                data: "lpb_date",
                className: "text-left",
            },
            {
                data: "keterangan",
                className: "text-left",
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return `<b>${greatFormatRupiah(data)}</b>`;
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
        },
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();
            const total = api.ajax.json().footerTotals || 0;

            if (total) {
                $('.total_masuk_detail').html(greatFormatRupiah(total.toFixed(2)));
            } else {
                $('.total_masuk_detail').html(greatFormatRupiah(0));

            }
        },
    });

    const tableKeluar = $('#dataTableKeluar').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [6, 'desc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-list/all-keluar-kartu-stock"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search_keluar").val();
                data.start_date = $("#start_date").val();
                data.end_date = $("#end_date").val();
                data.stock_id = stock_id;
                data.sort = sort;
                data.sortType = sortType;
            },

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
                data: "reference_tujuan_type",
                className: "text-left"
            },
            {
                data: "divisi_tujuan",
                className: "text-left"
            },
            {
                data: "warehouse_tujuan",
                className: "text-left",
            },
            {
                data: "reference_no",
                className: "text-left"
            },
            {
                data: "tanggal_keluar",
                className: "text-left"
            },
            {
                data: "keterangan",
                className: "text-left",
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return `<b>${greatFormatRupiah(data)}</b>`;
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
        },
        footerCallback: function(row, data, start, end, display) {
            const api = this.api();
            const total = api.ajax.json().footerTotals || 0;

            if (total) {
                $('.total_keluar_detail').html(greatFormatRupiah(total.toFixed(2)));
            } else {
                $('.total_keluar_detail').html(greatFormatRupiah(0));

            }
        },
    });

    $('#search_masuk').change((e) => {
        e.preventDefault();
        tableMasuk.ajax.reload()
    });

    $('#search_keluar').change((e) => {
        e.preventDefault();
        tableKeluar.ajax.reload()
    });

    $('#type_barang').select2({
        placeholder: "Pilih Tipe Barang",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function(e) {
        e.preventDefault();
        dropdownMasterBarang();
        table.ajax.reload();
    });

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function() {
        dropdownDivisi();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: false
    });

    $('#barang_master_id').select2({
        placeholder: "Cari Master Barang",
        theme: "bootstrap-5",
        allowClear: false,
    });


    $(".start_date,.end_date").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $('#start_date,#end_date,#barang_master_id,#divisi_id,#warehouse_id').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('#search_kartu').change(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });


    $("#type_barang,#divisi_id,#warehouse_id,#barang_master_id")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('#btnHidePemasukkan').click(function(e) {
        e.preventDefault();
        $('#detailPemasukkanModal').modal('hide');
    });


    $('#btnHidePengeluaran').click(function(e) {
        e.preventDefault();
        $('#detailPengeluaranModal').modal('hide');
    });

    $('#btnExportKartuStock').click(function(e) {
        e.preventDefault();
        var barangMasterId = $('#barang_master_id option:selected').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        if (barangMasterId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih master barang',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (start_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (end_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal selesai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var url = "<?= base_url('stock-list/export-kartu-stock') ?>" + "?barang_master_id=" + barangMasterId + "&start_date=" + start_date + "&end_date=" + end_date;
            window.location.href = url;
        }

    });

    $('#btnExportPemasukkan').click(function(e) {
        e.preventDefault();
        var stockId = stock_id;
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        if (stockId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih barang dulu',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (start_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (end_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal selesai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var url = "<?= base_url('stock-list/export-kartu-stock-masuk') ?>" + "?stock_id=" + stockId + "&start_date=" + start_date + "&end_date=" + end_date;
            window.location.href = url;
        }

    });

    $('#btnExportPengeluaran').click(function(e) {
        e.preventDefault();
        var stockId = stock_id;
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        if (stockId == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih barang dulu',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (start_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else if (end_date == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal selesai',
                confirmButtonColor: '#4e73df',
                confirmButtonText: 'Oke',
            });
            return;
        } else {
            var url = "<?= base_url('stock-list/export-kartu-stock-keluar') ?>" + "?stock_id=" + stockId + "&start_date=" + start_date + "&end_date=" + end_date;
            window.location.href = url;
        }

    });

    function detailMasuk(id) {
        $.ajax({
            url: `<?= base_url('stock-list/stock-identity-detail'); ?>`,
            method: "GET",
            data: {
                id: id,
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    var data = res.data;
                    var start_date = $('#start_date').val();
                    var end_date = $('#end_date').val();
                    $('#txt_date_range_masuk').text(`${start_date} s.d ${end_date}`);
                    $('#txt_kode_barang_masuk').text(`${data.kode_barang}`);
                    $('#txt_nama_barang_masuk').text(`${data.barang_name} / ${data.spesifikasi}`);
                    $('#txt_dept_masuk').text(`${data.divisi} ${data.warehouse_name}`);

                    stock_id = data.id;
                    tableMasuk.ajax.reload();
                    $('#detailPemasukkanModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    });
                }

            }
        });
    }

    function detailKeluar(id) {
        $.ajax({
            url: `<?= base_url('stock-list/stock-identity-detail'); ?>`,
            method: "GET",
            data: {
                id: id,
            },
            beforeSend: function() {
                setLoading();
            },
            complete: function() {
                stopLoading();
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    var data = res.data;
                    var start_date = $('#start_date').val();
                    var end_date = $('#end_date').val();
                    $('#txt_date_range_keluar').text(`${start_date} s.d ${end_date}`);
                    $('#txt_kode_barang_keluar').text(`${data.kode_barang}`);
                    $('#txt_nama_barang_keluar').text(`${data.barang_name} / ${data.spesifikasi}`);
                    $('#txt_dept_keluar').text(`${data.divisi} ${data.warehouse_name}`);

                    stock_id = data.id;
                    tableKeluar.ajax.reload();
                    $('#detailPengeluaranModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    });
                }

            }
        });
    }

    function dropdownMasterBarang() {
        $.ajax({
            url: `<?= base_url('stock-list/list-barang-master'); ?>`,
            method: "GET",
            data: {
                type_barang: $("#type_barang option:selected").val(),
            },
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".barang_master_id").empty()
                    $(".barang_master_id").append(`<option value=""></option>`)
                    res.data.forEach(function(item) {
                        $(".barang_master_id").append(`<option value="${item.id}">(${item.kode_barang}) ${item.barang_name}</option>`)
                    })
                    $(".barang_master_id").val();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    });
                }

            }
        });
    }

    function dropdownDivisi() {
        $.ajax({
            url: `<?= base_url('stock-list/warehouse'); ?>`,
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

    function changeSort(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>