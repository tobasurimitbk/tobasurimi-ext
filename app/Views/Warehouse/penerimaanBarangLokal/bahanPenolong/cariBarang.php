<?= $this->extend('layouts/template-new-window'); ?>
<?= $this->Section('content'); ?>
<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col mb-3">
                    <div class="alert alert-secondary">
                        <label class="form-label font-weight-bold text-black lable-title">
                            LIST BARANG BELUM DITERIMA
                        </label>
                    </div>
                </div>
            </div>
            <div class="row justify-content-end">
                <?= csrf_field() ?>
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="" class="form-control dateStart" id="dateStart" name="dateStart" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal SPP</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" value="" class="form-control dateEnd" id="dateEnd" name="dateEnd" aria-label="Floating label select example" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir SPP</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
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
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('createdAt')">No</th>
                                <th onclick="changeSort('division_id')">Dept</th>
                                <th onclick="changeSort('spp_no')">No Spp</th>
                                <th onclick="changeSort('request_date')">Tgl Spp</th>
                                <th onclick="changeSort('supplier_id')">Supplier</th>
                                <th onclick="changeSort('po_date')">Tgl Po</th>
                                <th onclick="changeSort('po_no')">No Po</th>
                                <th onclick="changeSort('barang_id')">Barang</th>
                                <th onclick="changeSort('spesifikasi_id')">Spesifikasi</th>
                                <th onclick="changeSort('note')">Note</th>
                                <th onclick="changeSort('qty')">Qty Order</th>
                                <th onclick="changeSort('qty_diterima')">Qty Diterima</th>
                                <th onclick="changeSort('remaining_qty')">Qty Sisa</th>
                                <th onclick="changeSort('unit')">Unit</th>
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
    let sort = "createdAt";
    let sortType = "desc";


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
            url: "<?= base_url("penerimaan-barang-lokal-bp/all-cari-barang"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.dateStart = $('.dateStart').val();
                data.dateEnd = $('.dateEnd').val();
                data.supplier_id = "<?= @$_GET['supplier_id'] ?>"
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
                data: "spp_no",
                className: "text-left"
            },
            {
                data: "request_date",
                className: "text-left"
            },
            {
                data: "supplier_name",
                className: "text-left",
            },
            {
                data: "po_date",
                className: "text-left"
            },
            {
                data: "po_no",
                className: "text-left"
            },
            {
                data: "barang_name",
                className: "text-left"
            },
            {
                data: "spesifikasi",
                className: "text-left"
            },
            {
                data: "note",
                className: "text-left"
            },
            {
                data: "qty",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
                }
            },
            {
                data: "qty_diterima",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
                }
            },
            {
                data: "remaining_qty",
                className: "text-left",
                render: function(data) {
                    return greatFormatRupiah(parseFloat(data).toFixed(2));
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


    $('#search').keyup(function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $(".dateStart, .dateEnd").datepicker({
        todayHighlight: true,
        format: "dd/mm/yyyy",
        orientation: "bottom auto",
        autoclose: true
    });

    $(".dateStart, .dateEnd").change(function(e) {
        e.preventDefault();
        table.ajax.reload()
    });
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>
<?= $this->endSection(); ?>