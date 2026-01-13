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
                        <input disabled autocomplete="one-time-code" value="<?= strtoupper(str_replace('_', ' ', $stock['type_barang']))  ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Tipe Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $stock['parent_name'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Kategori Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $stock['divisi'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Departemen</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $stock['warehouse_name'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Warehouse</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $stock['kode_barang'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Kode Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= $stock['barang_name'] . " - " . $stock['spesifikasi'] ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Barang - Spesifikasi</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input disabled autocomplete="one-time-code" value="<?= number_format($stock['qty_diterima'], 2) ?>" type="text" class="form-control" placeholder="">
                        <label for="floatingInput">Qty (<?= $stock['kode_satuan'] ?>)</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">DETAIL STOK BARANG</label>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
                <div class="col-md-4 mb-3">
                    <div class="form-floating">
                        <select class="form-select sumber_barang" id="sumber_barang" name="sumber_barang">
                            <option value=""></option>
                            <?php foreach ($sumberBarang as $s) : ?>
                                <option <?= $s == "PO LOKAL BAKU" ? "selected" : "" ?> value="<?= $s; ?>"><?= $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Sumber Barang</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="Cari Data" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable stok-dokumen-supplier-table" id="dataTable_supplier" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Supplier / Vendor</th>
                                <th>Kode Barang</th>
                                <th>Barang</th>
                                <th>Spesifikasi</th>
                                <th>Doc</th>
                                <th>No Po</th>
                                <th>Ref No</th>
                                <th>Tgl Po</th>
                                <th>No Daftar</th>
                                <th>No Aju</th>
                                <th>Qty</th>
                                <th>Unit</th>
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

<script>
    let sort = "stock_revamp_detail.id";
    let sortType = "desc";


    const csrfToken = '<?= csrf_token() ?>';
    const csrf = $(`[name="${csrfToken}"]`);

    const table = $('.dataTable').DataTable({
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
            url: "<?= base_url("stock-list/all-stock-detail"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.id = "<?= encrypt($stock['id']) ?>";
                data.sumber_barang = $("#sumber_barang option:selected").val();
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
                data: "supplier_name",
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
                data: "type_bc",
                className: "text-left"
            },
            {
                data: "po_no",
                className: "text-left"
            },
            {
                data: "ref_no",
                className: "text-left"
            },
            {
                data: "po_date",
                className: "text-left"
            },
            {
                data: "no_daftar",
                className: "text-left"
            },
            {
                data: "no_aju",
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


    $('#sumber_barang').select2({
        placeholder: "Pilih Sumber Barang",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload()
    });

    $('#search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $("#sumber_barang")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');
</script>
<?= $this->endSection(); ?>