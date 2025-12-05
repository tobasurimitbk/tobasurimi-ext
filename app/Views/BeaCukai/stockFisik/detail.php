<?= $this->extend('layouts/template-new-window'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL BARANG</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <?= csrf_field() ?>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper(str_replace('_', ' ', $barangMaster['type_barang']))  ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $barangMaster['parent_name'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $barangMaster['kode_barang'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Kode Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= trim($barangMaster['barang_name']) ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Barang</label>
                    </div>
                </div>
                <!-- <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Qty </label>
                    </div>
                </div> -->
            </div>
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKKAN PER DOKUMEN</label>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-md-3 mb-3">

                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="Cari Data" class="form-control search_masuk" id="search_masuk" name="search_masuk" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-supplier-table" id="dataTableMasuk" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Supplier</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Doc</th>
                                <th>No Po</th>
                                <th>Ref No</th>
                                <th>Tgl Masuk</th>
                                <th>No Daftar</th>
                                <th>No Aju</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Valas</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="13" class="text-right">TOTAL</th>
                                <th id="total_qty_masuk"></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DATA PEMASUKKAN BARANG DARI PRODUKSI</label>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-md-3 mb-3">

                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="Cari Data" class="form-control search_masuk_produksi" id="search_masuk_produksi" name="search_masuk_produksi" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-supplier-table" id="dataTableMasukProduksi" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 10px;">No</th>
                                <th>Dept</th>
                                <th>Warehouse</th>
                                <th>Tgl Produksi</th>
                                <th>Kode Produksi</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Qty</th>
                                <th>Unit</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-right">TOTAL</th>
                                <th id="total_qty_masuk_produksi"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var tableMasuk = $('#dataTableMasuk').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('stock-fisik/all-masuk') ?>",
            type: "GET",
            data: function(d) {
                d.search = $('#search_masuk').val();
                d.barang_master_id = "<?= ($barangMaster['id']) ?>"
            }
        },
        order: [
            [2, 'desc']
        ],
        columns: [{
                data: 'no',
                orderable: false
            },
            {
                data: 'divisi'
            },
            {
                data: 'warehouse_name'
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'spesifikasi'
            },
            {
                data: 'jenis_doc'
            },
            {
                data: 'po_no'
            },
            {
                data: 'ref_no'
            },
            {
                data: 'tanggal_lpb'
            },
            {
                data: 'no_daftar'
            },
            {
                data: 'no_aju'
            },
            {
                data: 'qty_diterima',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'kode_satuan'
            },
            {
                data: 'valas'
            },
            {
                data: 'total_harga',
                className: 'text-end total-col',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2) || 0);
                    }
                    return "";
                }
            },
        ],
        display: "stripe",
        searching: false,
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
                $('#total_qty_masuk').html(greatFormatRupiah(total.toFixed(2)));
            } else {
                $('#total_qty_masuk').html(greatFormatRupiah(0));

            }
        },
    });

    var tableMasukProduksi = $('#dataTableMasukProduksi').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url('stock-fisik/all-masuk-produksi') ?>",
            type: "GET",
            data: function(d) {
                d.search = $('#search_masuk_produksi').val();
                d.barang_master_id = "<?= ($barangMaster['id']) ?>"
            }
        },
        order: [
            [3, 'desc']
        ],
        columns: [{
                data: 'no',
                orderable: false
            },
            {
                data: 'divisi'
            },
            {
                data: 'warehouse_name'
            },
            {
                data: 'receive_date'
            },
            {
                data: 'pr_no'
            },
            {
                data: 'kode_barang'
            },
            {
                data: 'barang_name'
            },
            {
                data: 'spesifikasi'
            },
            {
                data: 'qty_diterima',
                render: function(data) {
                    if (data != "") {
                        return greatFormatRupiah(parseFloat(data).toFixed(2));
                    }
                    return "";
                }
            },
            {
                data: 'kode_satuan'
            },
        ],
        display: "stripe",
        searching: false,
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
                $('#total_qty_masuk_produksi').html(greatFormatRupiah(total.toFixed(2)));
            } else {
                $('#total_qty_masuk_produksi').html(greatFormatRupiah(0));

            }
        },
    });

    $('#search_masuk').keyup(function(e) {
        e.preventDefault();
        tableMasuk.ajax.reload();
    });

    $('#search_masuk_produksi').keyup(function(e) {
        e.preventDefault();
        tableMasukProduksi.ajax.reload();
    });
</script>
<?= $this->endSection(); ?>