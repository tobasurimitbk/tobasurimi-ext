<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Laporan Purchase Order</h1>

        <div class="col-button-tambah-spp">
            <a class="btn btn-warning btn-print float-right" href="#" id="btnExport">
                <i class="fa fa-download"></i> Export
            </a>
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("laporan-warehouse"); ?>">
                Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-start">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select po_type" name="po_type" id="po_type">
                            <option selected value="LOKAL BB">PO LOKAL BB</option>
                            <option value="LOKAL BP">PO LOKAL BP</option>
                            <option value="IMPORT BB">PO IMPORT BB</option>
                            <option value="IMPORT BP">PO IMPORT BP</option>
                        </select>
                        <label for="floatingInput">Filter Tipe PO</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select status_posting" name="status_posting" id="status_posting">
                            <option selected value="ALL">SEMUA</option>
                            <option value="BELUM POSTING">BELUM POSTING</option>
                            <option value="SUDAH POSTING">SUDAH POSTING</option>
                        </select>
                        <label for="floatingInput">Filter Status Posting</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="01/<?= date('m/Y') ?>" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
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
                            <input placeholder="" value="31/<?= date('m/Y') ?>" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select divisi_id" name="divisi_id" id="divisi_id">
                            <option value=""></option>
                            <?php foreach ($divisis as $divisi) : ?>
                                <option value="<?= $divisi['id']; ?>"><?= $divisi['divisi']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select supplier_id" name="supplier_id" id="supplier_id">
                            <option value=""></option>
                            <?php foreach ($suppliers as $supplier) : ?>
                                <option value="<?= $supplier['id']; ?>"><?= $supplier['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Filter Supplier</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" aria-label="Floating label select example" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th onclick="changeSort('divisi')" class="sort">Dept</th>
                                <th onclick="changeSort('po_date')" class="sort">Tgl PO</th>
                                <th onclick="changeSort('po_no')" class="sort">No PO</th>
                                <th onclick="changeSort('spp_no')" class="sort">No SPP</th>
                                <th onclick="changeSort('supplier_id')" class="sort">Supplier</th>
                                <th onclick="changeSort('kode_barang')" class="sort">Kode Barang</th>
                                <th onclick="changeSort('barang_name')" class="sort">Nama Barang</th>
                                <th onclick="changeSort('spesifikasi')" class="sort">Spesifikasi</th>
                                <th onclick="changeSort('satuan_id')" class="sort">Satuan</th>
                                <th onclick="changeSort('uraian')" class="sort">Keterangan</th>
                                <th onclick="changeSort('qty_order')" class="sort">Jml Order</th>
                                <th onclick="changeSort('qty_diterima')" class="sort">Jml Diterima</th>
                                <th onclick="changeSort('qty_sisa')" class="sort">Sisa</th>
                                <th onclick="changeSort('total_harga')" class="sort">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table">
                        </tbody>
                        <tfoot id="grandTotalHargaPrev">
                            <tr>
                                <th colspan="14" class="text-right">GRAND TOTAL</th>
                                <th class="text-left grandTotalHarga"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "po_date";
    let sortType = "desc";
    var row = 0;

    var table = $('.dataTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("/laporan-warehouse/purchase-order/all-purchase-order"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.sort = sort;
                data.sortType = sortType;
                data.dateStart = $(".dateStart").val();
                data.dateEnd = $(".dateEnd").val();
                data.po_type = $(".po_type").val();
                data.divisi_id = $(".divisi_id").val();
                data.supplier_id = $(".supplier_id").val();
                data.status_posting = $(".status_posting").val();
                data.search = $(".search").val();

            },
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-left",
                sortable: false
            },
            {
                data: "divisi",
                className: "text-left",

            },
            {
                data: "po_date",
                className: "text-left",
            },
            {
                data: "po_no",
                className: "text-left",
            },
            {
                data: "spp_no",
                className: "text-left",
            },
            {
                data: "supplier_name",
                className: "text-left",
            },
            {
                data: "kode_barang",
                className: "text-left",
            },
            {
                data: "barang_name",
                className: "text-left",
            },
            {
                data: "spesifikasi",
                className: "text-left",
            },
            {
                data: "kode_satuan",
                className: "text-left",
            },
            {
                data: "uraian",
                className: "text-left",
            },
            {
                data: "qty_order",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data.toFixed(2));
                }
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data.toFixed(2));
                }
            },
            {
                data: "qty_sisa",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(data.toFixed(2));
                }
            },
            {
                data: "total_harga",
                className: "text-left",
            },
        ],
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
            const grandTotalHarga = api.ajax.json().grandTotalHarga;
            $('.grandTotalHarga').html(greatFormatRupiah(grandTotalHarga.toFixed(2)));
        },
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    $(".dateStart").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $(".dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    })

    $('.icon-dateStart').click(function() {
        $(".dateStart").focus();
    });

    $('.icon-dateEnd').click(function() {
        $(".dateEnd").focus();
    });

    $(".search").change(function() {
        table.ajax.reload();
    })

    $(".dateStart, .dateEnd, .po_type, .divisi_id, .supplier_id,.status_posting").change(function() {
        table.ajax.reload();
    });

    $('.po_type').select2({
        placeholder: "Filter Tipe PO",
        theme: "bootstrap-5",
        allowClear: false
    }).change(function(e) {
        e.preventDefault();
        var poType = $('.po_type option:selected').val();
        if (poType == "LOKAL BB" || poType == "LOKAL BP") {
            $('#grandTotalHargaPrev').show();
        } else {
            $('#grandTotalHargaPrev').hide();
        }
    });

    $('.divisi_id').select2({
        placeholder: "Filter Departemen",
        theme: "bootstrap-5",
        allowClear: true
    });

    $('.supplier_id').select2({
        placeholder: "Filter Supplier",
        theme: "bootstrap-5",
        allowClear: true
    });
    $('.status_posting').select2({
        placeholder: "Filter Supplier",
        theme: "bootstrap-5",
        allowClear: false
    });

    $('.po_type, .divisi_id, .supplier_id,.status_posting')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.po_type, .divisi_id, .supplier_id,.status_posting')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.po_type, .divisi_id, .supplier_id,.status_posting')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('#btnExport').click(function(e) {
        e.preventDefault();
        let dateStart = $(".dateStart").val();
        let dateEnd = $(".dateEnd").val();
        let po_type = $(".po_type").val();
        let divisi_id = $(".divisi_id").val();
        let supplier_id = $(".supplier_id").val();
        let search = $(".search").val();
        let status_posting = $('.status_posting').val();

        if (dateStart == '' || dateEnd == '') {
            Swal.fire({
                icon: 'error',
                title: 'Pilih tanggal mulai & tanggal selesai',
                confirmButtonColor: '#4e73df',
            });
        } else {
            var url = "<?= base_url('laporan-warehouse/purchase-order/excel') ?>" + '?dateStart=' + dateStart + '&dateEnd=' + dateEnd + '&po_type=' + po_type + '&divisi_id=' + divisi_id + '&supplier_id=' + supplier_id + '&search=' + search + '&status_posting=' + status_posting;
            window.open(url);
        }

    })
    const pdf = function(url) {




    }
</script>

<?= $this->endSection(); ?>