<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Stock Histori</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select divisi_id" id="divisi_id" name="divisi_id">
                            <option value=""></option>
                            <?php foreach ($dataDivisi as $divisi) : ?>
                                <option value="<?= $divisi["id"]; ?>"><?= strtoupper($divisi["divisi"]); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label style="z-index: 1;">Departemen</label>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
                    <div class="form-floating">
                        <select class="form-select warehouse_id" id="warehouse_id" name="warehouse_id">

                        </select>
                        <label style="z-index: 1;">Warehouse</label>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateStart" id="dateStart" name="dateStart" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Awal</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-2">
                    <div class="input-group">
                        <div class="form-floating" style="height: 50px;">
                            <input placeholder="" class="form-control dateEnd" id="dateEnd" name="dateEnd" />
                            <label style="z-index: 1;" style="z-index: 1;">Tanggal Akhir</label>
                        </div>
                        <div class="input-group-append" style="height:50px;">
                            <button disabled class="btn btn-secondary" type="button">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 mt-2">
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
                <div class="col-sm-4 mt-2">
                    <div class="form-floating" style="height: 50px;">
                        <input placeholder="" class="form-control search" id="search" name="search" />
                        <label style="z-index: 1;" style="z-index: 1;">Cari Data </label>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th onclick="changeSort('supplier_id')">Supplier / Vendor</th>
                            <th onclick="changeSort('kode_barang')">Kode Barang</th>
                            <th onclick="changeSort('barang_name')">Barang</th>
                            <th onclick="changeSort('spesifikasi')">Spesifikasi</th>
                            <th onclick="changeSort('divisi_id')">Dept</th>
                            <th onclick="changeSort('warehouse_id')">Warehouse</th>
                            <th onclick="changeSort('type_bc')">Doc</th>
                            <th onclick="changeSort('po_id')">No Po</th>
                            <th onclick="changeSort('reference_id')">Ref No</th>
                            <th onclick="changeSort('tanggal_po')">Tgl Po</th>
                            <th onclick="changeSort('createdAt')">Tgl Log</th>
                            <th onclick="changeSort('qty_diterima')">Qty</th>
                            <th onclick="changeSort('unit_id')">Unit</th>
                        </tr>
                    </thead>
                    <tbody class="body-table" id="body-table">
                    </tbody>
                </table>
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
            url: "<?= base_url("stock-histori/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.divisi_id = $("#divisi_id option:selected").val();
                data.warehouse_id = $("#warehouse_id option:selected").val();
                data.dateStart = $('#dateStart').val();
                data.dateEnd = $('#dateEnd').val();
                data.sumber_barang = $('#sumber_barang option:selected').val();
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
                data: "divisi",
                className: "text-left",
            },
            {
                data: "warehouse_name",
                className: "text-left",
            },
            {
                data: "type_bc",
                className: "text-left"
            },
            {
                data: "po_no",
                className: "text-left",
            },
            {
                data: "ref_no",
                className: "text-left",
            },
            {
                data: "tanggal_po",
                className: "text-left",
            },
            {
                data: "createdAt",
                className: "text-left",
            },
            {
                data: "qty",
                className: "text-left",
                render: function(data, type, row) {
                    let status = row.status;
                    let qty = row.qty;
                    let simbol = "";

                    if (status == "IN") {
                        simbol = "(+)";
                    } else {
                        simbol = "(-)";
                    }

                    return simbol + " " + greatFormatRupiah(data);
                }
            },
            {
                data: "kode_satuan",
                className: "text-left",
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

    $('#divisi_id').select2({
        placeholder: "Pilih Departemen",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        // GET WAREHOUSES
        $.ajax({
            url: `<?= base_url('stock-list/warehouse'); ?>`,
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
        table.ajax.reload();
    });

    $('#warehouse_id').select2({
        placeholder: "Pilih Warehouse",
        theme: "bootstrap-5",
        allowClear: true
    }).change(function() {
        table.ajax.reload();
    });

    $('#sumber_barang').select2({
        placeholder: "Pilih Sumber Barang",
        theme: "bootstrap-5",
    }).change(function() {
        table.ajax.reload();
    });

    $('.search').keyup(function() {
        table.ajax.reload();
    });

    $('#dateStart,#dateEnd').change(function() {
        table.ajax.reload();
    });

    $("#parent_type,#divisi_id,#warehouse_id,#sumber_barang")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    const stokDetail = function(id) {
        location.replace("<?= base_url('stock-list/id/') ?>" + id, "");
    }

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